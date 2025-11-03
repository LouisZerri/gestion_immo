<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Proprietaire;
use App\Http\Requests\BienRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BienController extends Controller
{
    /**
     * Liste des biens avec filtres
     */
    public function index(Request $request)
    {
        $query = Bien::with('proprietaire');

        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%")
                  ->orWhere('code_postal', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par propriétaire
        if ($request->filled('proprietaire_id')) {
            $query->where('proprietaire_id', $request->proprietaire_id);
        }

        // Filtre par ville
        if ($request->filled('ville')) {
            $query->where('ville', 'like', "%{$request->ville}%");
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $biens = $query->paginate(12);

        // Listes pour les filtres
        $proprietaires = Proprietaire::orderBy('nom')->get();
        $types = $this->getTypes();
        $statuts = $this->getStatuts();

        return view('biens.index', compact('biens', 'proprietaires', 'types', 'statuts'));
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        $proprietaires = Proprietaire::orderBy('nom')->get();
        $types = $this->getTypes();
        
        // Pré-sélection du propriétaire si passé en paramètre
        $selectedProprietaire = $request->get('proprietaire');

        return view('biens.create', compact('proprietaires', 'types', 'selectedProprietaire'));
    }

    /**
     * Enregistrer un nouveau bien
     */
    public function store(BienRequest $request)
    {
        $data = $request->validated();

        // Générer une référence unique si non fournie
        if (empty($data['reference'])) {
            $data['reference'] = 'BIEN-' . strtoupper(uniqid());
        }

        $bien = Bien::create($data);

        // Gérer l'upload de photos
        if ($request->hasFile('photos')) {
            $this->uploadPhotos($bien, $request->file('photos'));
        }

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', 'Bien créé avec succès !');
    }

    /**
     * Afficher un bien
     */
    public function show(Bien $bien)
    {
        // Charger les relations
        $bien->load(['proprietaire', 'contrats.locataires']);
        
        // Statistiques
        $stats = [
            'contrats_total' => $bien->contrats()->count(),
            'contrats_actifs' => $bien->contrats()->where('statut', 'actif')->count(),
            'loyer_actuel' => $bien->contrats()->where('statut', 'actif')->sum('loyer_cc'),
            'documents_total' => $bien->documents()->count(),
        ];

        // Historique des contrats
        $contrats = $bien->contrats()
            ->with('locataires')
            ->latest()
            ->get();

        // Photos
        $photos = $bien->photos ?? [];

        return view('biens.show', compact('bien', 'stats', 'contrats', 'photos'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Bien $bien)
    {
        $proprietaires = Proprietaire::orderBy('nom')->get();
        $types = $this->getTypes();
        $photos = $bien->photos ?? [];

        return view('biens.edit', compact('bien', 'proprietaires', 'types', 'photos'));
    }

    /**
     * Mettre à jour un bien
     */
    public function update(BienRequest $request, Bien $bien)
    {
        $data = $request->validated();
        
        $bien->update($data);

        // Gérer l'upload de nouvelles photos
        if ($request->hasFile('photos')) {
            $this->uploadPhotos($bien, $request->file('photos'));
        }

        // Supprimer des photos si demandé
        if ($request->filled('delete_photos')) {
            $this->deletePhotos($bien, $request->delete_photos);
        }

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', 'Bien mis à jour avec succès !');
    }

    /**
     * Supprimer un bien (soft delete)
     */
    public function destroy(Bien $bien)
    {
        // Vérifier qu'il n'a pas de contrats actifs
        if ($bien->contrats()->where('statut', 'actif')->count() > 0) {
            return redirect()
                ->route('biens.index')
                ->with('error', 'Impossible de supprimer ce bien car il a des contrats actifs.');
        }

        // Supprimer les photos
        if (!empty($bien->photos)) {
            foreach ($bien->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $bien->delete();

        return redirect()
            ->route('biens.index')
            ->with('success', 'Bien supprimé avec succès !');
    }

    /**
     * Upload de photos
     */
    private function uploadPhotos(Bien $bien, array $photos)
    {
        $uploadedPhotos = $bien->photos ?? [];

        foreach ($photos as $photo) {
            $path = $photo->store('biens/' . $bien->id, 'public');
            $uploadedPhotos[] = $path;
        }

        $bien->update(['photos' => $uploadedPhotos]);
    }

    /**
     * Supprimer des photos
     */
    private function deletePhotos(Bien $bien, array $photosToDelete)
    {
        $currentPhotos = $bien->photos ?? [];
        
        foreach ($photosToDelete as $photoPath) {
            // Supprimer du storage
            Storage::disk('public')->delete($photoPath);
            
            // Retirer du tableau
            $currentPhotos = array_diff($currentPhotos, [$photoPath]);
        }

        $bien->update(['photos' => array_values($currentPhotos)]);
    }

    /**
     * Types de biens
     */
    private function getTypes()
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'studio' => 'Studio',
            'parking' => 'Parking',
            'garage' => 'Garage',
            'local_commercial' => 'Local commercial',
            'bureau' => 'Bureau',
            'terrain' => 'Terrain',
        ];
    }

    /**
     * Statuts de biens
     */
    private function getStatuts()
    {
        return [
            'disponible' => 'Disponible',
            'loue' => 'Loué',
            'en_travaux' => 'En travaux',
            'vendu' => 'Vendu',
        ];
    }
}