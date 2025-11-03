<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Http\Requests\ProprietaireRequest;
use Illuminate\Http\Request;

class ProprietaireController extends Controller
{
    /**
     * Liste des propriétaires avec filtres
     */
    public function index(Request $request)
    {
        $query = Proprietaire::query();

        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('nom_societe', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut de mandat
        if ($request->filled('mandat')) {
            $query->where('mandat_actif', $request->mandat === 'actif');
        }

        // Tri par défaut : nom
        $proprietaires = $query->withCount('biens')
                               ->orderBy('nom', 'asc')
                               ->paginate(15);

        return view('proprietaires.index', compact('proprietaires'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('proprietaires.create');
    }

    /**
     * Enregistrer un nouveau propriétaire
     */
    public function store(ProprietaireRequest $request)
    {
        $data = $request->validated();

        // Gérer les champs conditionnels selon le type
        if ($data['type'] === 'particulier') {
            $data['nom_societe'] = null;
            $data['siret'] = null;
        } else {
            $data['prenom'] = null;
        }

        $proprietaire = Proprietaire::create($data);

        return redirect()
            ->route('proprietaires.show', $proprietaire)
            ->with('success', 'Propriétaire créé avec succès !');
    }

    /**
     * Afficher un propriétaire
     */
    public function show(Proprietaire $proprietaire)
    {
        // Charger les relations avec compteurs
        $proprietaire->loadCount(['biens', 'contrats', 'documents']);
        
        // Charger les biens avec leurs derniers contrats
        $biens = $proprietaire->biens()
            ->with(['contrats' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        // Statistiques
        $stats = [
            'total_biens' => $proprietaire->biens_count,
            'biens_loues' => $proprietaire->biens()->where('statut', 'loue')->count(),
            'biens_disponibles' => $proprietaire->biens()->where('statut', 'disponible')->count(),
            'contrats_actifs' => $proprietaire->contrats()->where('statut', 'actif')->count(),
            'revenus_mensuels' => $proprietaire->contrats()
                ->where('statut', 'actif')
                ->sum('loyer_cc'),
        ];

        // Derniers contrats
        $contrats = $proprietaire->contrats()
            ->with(['bien', 'locataires'])
            ->latest()
            ->limit(5)
            ->get();

        return view('proprietaires.show', compact('proprietaire', 'biens', 'stats', 'contrats'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Proprietaire $proprietaire)
    {
        return view('proprietaires.edit', compact('proprietaire'));
    }

    /**
     * Mettre à jour un propriétaire
     */
    public function update(ProprietaireRequest $request, Proprietaire $proprietaire)
    {
        $data = $request->validated();

        // Gérer les champs conditionnels selon le type
        if ($data['type'] === 'particulier') {
            $data['nom_societe'] = null;
            $data['siret'] = null;
        } else {
            $data['prenom'] = null;
        }

        $proprietaire->update($data);

        return redirect()
            ->route('proprietaires.show', $proprietaire)
            ->with('success', 'Propriétaire mis à jour avec succès !');
    }

    /**
     * Supprimer un propriétaire (soft delete)
     */
    public function destroy(Proprietaire $proprietaire)
    {
        // Vérifier qu'il n'a pas de biens actifs
        if ($proprietaire->biens()->count() > 0) {
            return redirect()
                ->route('proprietaires.index')
                ->with('error', 'Impossible de supprimer ce propriétaire car il possède des biens. Veuillez d\'abord supprimer ou réaffecter ses biens.');
        }

        $proprietaire->delete();

        return redirect()
            ->route('proprietaires.index')
            ->with('success', 'Propriétaire supprimé avec succès !');
    }

    /**
     * Toggle le statut du mandat
     */
    public function toggleMandat(Proprietaire $proprietaire)
    {
        $proprietaire->update([
            'mandat_actif' => !$proprietaire->mandat_actif,
            'date_debut_mandat' => !$proprietaire->mandat_actif ? now() : $proprietaire->date_debut_mandat,
        ]);

        $status = $proprietaire->mandat_actif ? 'activé' : 'désactivé';

        return redirect()
            ->back()
            ->with('success', "Mandat {$status} avec succès !");
    }
}