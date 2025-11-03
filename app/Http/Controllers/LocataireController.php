<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\Garant;
use App\Http\Requests\LocataireRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocataireController extends Controller
{
    /**
     * Liste des locataires avec filtres
     */
    public function index(Request $request)
    {
        $query = Locataire::query();

        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('profession', 'like', "%{$search}%");
            });
        }

        // Filtre par ville
        if ($request->filled('ville')) {
            $query->where('ville', 'like', "%{$request->ville}%");
        }

        // Filtre avec/sans garant
        if ($request->filled('avec_garant')) {
            if ($request->avec_garant === 'oui') {
                $query->has('garants');
            } elseif ($request->avec_garant === 'non') {
                $query->doesntHave('garants');
            }
        }

        // Tri
        $locataires = $query->withCount(['contrats', 'garants'])
                            ->orderBy('nom', 'asc')
                            ->paginate(15);

        return view('locataires.index', compact('locataires'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('locataires.create');
    }

    /**
     * Enregistrer un nouveau locataire
     */
    public function store(LocataireRequest $request)
    {
        $data = $request->validated();
        
        // Créer le locataire
        $locataire = Locataire::create($data);

        // Gérer les garants si présents
        if ($request->has('garants')) {
            $this->syncGarants($locataire, $request->garants);
        }

        return redirect()
            ->route('locataires.show', $locataire)
            ->with('success', 'Locataire créé avec succès !');
    }

    /**
     * Afficher un locataire
     */
    public function show(Locataire $locataire)
    {
        // Charger les relations
        $locataire->load(['garants', 'contrats.bien', 'documents']);
        
        // Statistiques
        $stats = [
            'contrats_total' => $locataire->contrats()->count(),
            'contrats_actifs' => $locataire->contrats()->where('statut', 'actif')->count(),
            'garants_total' => $locataire->garants()->count(),
            'documents_total' => $locataire->documents()->count(),
            'loyer_mensuel' => $locataire->contrats()
                ->where('statut', 'actif')
                ->sum('loyer_cc'),
        ];

        // Contrats
        $contrats = $locataire->contrats()
            ->with('bien')
            ->latest()
            ->get();

        return view('locataires.show', compact('locataire', 'stats', 'contrats'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Locataire $locataire)
    {
        $locataire->load('garants');
        return view('locataires.edit', compact('locataire'));
    }

    /**
     * Mettre à jour un locataire
     */
    public function update(LocataireRequest $request, Locataire $locataire)
    {
        $data = $request->validated();
        
        $locataire->update($data);

        // Gérer les garants
        if ($request->has('garants')) {
            $this->syncGarants($locataire, $request->garants);
        }

        return redirect()
            ->route('locataires.show', $locataire)
            ->with('success', 'Locataire mis à jour avec succès !');
    }

    /**
     * Supprimer un locataire (soft delete)
     */
    public function destroy(Locataire $locataire)
    {
        // Vérifier qu'il n'a pas de contrats actifs
        if ($locataire->contrats()->where('statut', 'actif')->count() > 0) {
            return redirect()
                ->route('locataires.index')
                ->with('error', 'Impossible de supprimer ce locataire car il a des contrats actifs.');
        }

        $locataire->delete();

        return redirect()
            ->route('locataires.index')
            ->with('success', 'Locataire supprimé avec succès !');
    }

    /**
     * Synchroniser les garants
     */
    private function syncGarants(Locataire $locataire, array $garantsData)
    {
        // Supprimer les garants existants
        $locataire->garants()->delete();

        // Créer les nouveaux garants
        foreach ($garantsData as $garantData) {
            // Ignorer les garants vides
            if (empty($garantData['nom']) || empty($garantData['prenom'])) {
                continue;
            }

            $locataire->garants()->create($garantData);
        }
    }
}