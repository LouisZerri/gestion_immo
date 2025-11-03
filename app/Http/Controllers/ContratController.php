<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContratRequest;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\DocumentTemplate;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    /**
     * @var DocumentGeneratorService
     */
    protected $documentGenerator;

    /**
     * Constructor
     * 
     * @param DocumentGeneratorService $documentGenerator
     */
    public function __construct(DocumentGeneratorService $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    /**
     * Liste des contrats avec filtres
     */
    public function index(Request $request)
    {
        $query = Contrat::with(['bien', 'proprietaire', 'locataires']);

        // Filtre : Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('bien', function ($q) use ($search) {
                        $q->where('adresse', 'like', "%{$search}%");
                    })
                    ->orWhereHas('locataires', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre : Statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre : Type de bail
        if ($request->filled('type_bail')) {
            $query->where('type_bail', $request->type_bail);
        }

        // Filtre : Propriétaire
        if ($request->filled('proprietaire_id')) {
            $query->where('proprietaire_id', $request->proprietaire_id);
        }

        // Filtre : Bien
        if ($request->filled('bien_id')) {
            $query->where('bien_id', $request->bien_id);
        }

        // Tri par date de début décroissante
        $query->orderBy('date_debut', 'desc');

        $contrats = $query->paginate(15)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Contrat::count(),
            'actifs' => Contrat::where('statut', 'actif')->count(),
            'resilie' => Contrat::where('statut', 'resilie')->count(),
            'expire_bientot' => Contrat::where('statut', 'actif')
                ->whereDate('date_fin', '<=', now()->addMonths(3))
                ->count(),
        ];

        // Données pour les filtres
        $proprietaires = Proprietaire::orderBy('nom')->get();
        $biens = Bien::with('proprietaire')->orderBy('reference')->get();

        return view('contrats.index', compact('contrats', 'stats', 'proprietaires', 'biens'));
    }

    /**
     * Afficher le formulaire de création (wizard étape 1)
     */
    public function create(Request $request)
    {
        $step = $request->get('step', 1);
        
        // Données pour étape 1 : Sélection du bien
        $biens = Bien::with('proprietaire')
            ->where('statut', '!=', 'vendu')
            ->orderBy('reference')
            ->get();

        // Données pour étape 2 : Sélection des locataires
        $locataires = Locataire::with('garants')->orderBy('nom')->get();

        // Récupération des données de session si on revient en arrière
        $selectedBien = $request->session()->get('contrat_bien_id');
        $selectedLocataires = $request->session()->get('contrat_locataires', []);
        $contratData = $request->session()->get('contrat_data', []);

        return view('contrats.create', compact(
            'step',
            'biens',
            'locataires',
            'selectedBien',
            'selectedLocataires',
            'contratData'
        ));
    }

    /**
     * Enregistrer le contrat (toutes les étapes)
     */
    public function store(ContratRequest $request)
    {
        try {
            DB::beginTransaction();

            // 1. Créer le contrat
            $contrat = Contrat::create([
                'reference' => $this->generateReference(),
                'bien_id' => $request->bien_id,
                'proprietaire_id' => $request->proprietaire_id,
                'type_bail' => $request->type_bail,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'duree_mois' => $request->duree_mois,
                'loyer_hc' => $request->loyer_hc,
                'charges' => $request->charges ?? 0,
                'loyer_cc' => ($request->loyer_hc + ($request->charges ?? 0)),
                'depot_garantie' => $request->depot_garantie,
                'periodicite_paiement' => $request->periodicite_paiement,
                'jour_paiement' => $request->jour_paiement,
                'indice_reference' => $request->indice_reference,
                'date_revision' => $request->date_revision,
                'tacite_reconduction' => $request->has('tacite_reconduction'),
                'statut' => 'actif',
                'date_signature' => $request->date_signature ?? now(),
                'conditions_particulieres' => $request->conditions_particulieres,
            ]);

            // 2. Associer les locataires avec leurs parts de loyer
            $locataires = $request->locataires; // Array d'IDs
            $partsLoyer = $request->parts_loyer ?? []; // Array de pourcentages

            foreach ($locataires as $index => $locataireId) {
                $contrat->locataires()->attach($locataireId, [
                    'titulaire_principal' => ($index === 0), // Le premier est principal
                    'part_loyer' => $partsLoyer[$index] ?? (100 / count($locataires)),
                ]);
            }

            // 3. Mettre à jour le statut du bien
            $contrat->bien->update(['statut' => 'loue']);

            // 4. Générer automatiquement les documents de base (optionnel)
            if ($request->has('generer_documents')) {
                $this->genererDocumentsInitiaux($contrat);
            }

            DB::commit();

            // Nettoyer la session
            $request->session()->forget(['contrat_bien_id', 'contrat_locataires', 'contrat_data']);

            // ✅ NOUVEAU : Proposer génération automatique du bail
            session()->flash('success', 'Contrat créé avec succès !');
            session()->flash('show_generate_modal', true);

            return redirect()->route('contrats.show', $contrat);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du contrat : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un contrat
     */
    public function show(Contrat $contrat)
    {
        $contrat->load([
            'bien.proprietaire',
            'proprietaire',
            'locataires.garants',
            'documents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Calculer quelques statistiques
        $stats = [
            'nb_locataires' => $contrat->locataires->count(),
            'nb_garants' => $contrat->locataires->sum(fn($l) => $l->garants->count()),
            'nb_documents' => $contrat->documents->count(),
            'loyer_annuel' => $contrat->loyer_cc * 12,
            'jours_restants' => now()->diffInDays($contrat->date_fin, false),
        ];

        return view('contrats.show', compact('contrat', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Contrat $contrat)
    {
        $contrat->load(['bien', 'proprietaire', 'locataires']);

        $biens = Bien::with('proprietaire')->orderBy('reference')->get();
        $locataires = Locataire::orderBy('nom')->get();
        $proprietaires = Proprietaire::orderBy('nom')->get();

        return view('contrats.edit', compact('contrat', 'biens', 'locataires', 'proprietaires'));
    }

    /**
     * Mettre à jour un contrat
     */
    public function update(ContratRequest $request, Contrat $contrat)
    {
        try {
            DB::beginTransaction();

            // 1. Mettre à jour le contrat
            $contrat->update([
                'bien_id' => $request->bien_id,
                'proprietaire_id' => $request->proprietaire_id,
                'type_bail' => $request->type_bail,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'duree_mois' => $request->duree_mois,
                'loyer_hc' => $request->loyer_hc,
                'charges' => $request->charges ?? 0,
                'loyer_cc' => ($request->loyer_hc + ($request->charges ?? 0)),
                'depot_garantie' => $request->depot_garantie,
                'periodicite_paiement' => $request->periodicite_paiement,
                'jour_paiement' => $request->jour_paiement,
                'indice_reference' => $request->indice_reference,
                'date_revision' => $request->date_revision,
                'tacite_reconduction' => $request->has('tacite_reconduction'),
                'date_signature' => $request->date_signature,
                'conditions_particulieres' => $request->conditions_particulieres,
            ]);

            // 2. Resynchroniser les locataires
            $locataires = $request->locataires;
            $partsLoyer = $request->parts_loyer ?? [];
            
            $syncData = [];
            foreach ($locataires as $index => $locataireId) {
                $syncData[$locataireId] = [
                    'titulaire_principal' => ($index === 0),
                    'part_loyer' => $partsLoyer[$index] ?? (100 / count($locataires)),
                ];
            }
            
            $contrat->locataires()->sync($syncData);

            DB::commit();

            return redirect()
                ->route('contrats.show', $contrat)
                ->with('success', 'Contrat mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un contrat
     */
    public function destroy(Contrat $contrat)
    {
        try {
            // Vérifier qu'on peut supprimer
            if ($contrat->documents()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer un contrat avec des documents associés.');
            }

            // Détacher les locataires
            $contrat->locataires()->detach();

            // Mettre à jour le statut du bien
            $contrat->bien->update(['statut' => 'disponible']);

            // Supprimer le contrat (soft delete)
            $contrat->delete();

            return redirect()
                ->route('contrats.index')
                ->with('success', 'Contrat supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Résilier un contrat
     */
    public function resilier(Request $request, Contrat $contrat)
    {
        $request->validate([
            'date_resiliation' => 'required|date|after_or_equal:' . $contrat->date_debut,
            'motif_resiliation' => 'nullable|string|max:1000',
        ]);

        try {
            $contrat->update([
                'statut' => 'resilie',
                'date_fin' => $request->date_resiliation,
                'conditions_particulieres' => $contrat->conditions_particulieres . "\n\n--- RÉSILIATION ---\n" 
                    . "Date : " . $request->date_resiliation . "\n"
                    . "Motif : " . ($request->motif_resiliation ?? 'Non précisé'),
            ]);

            // Libérer le bien
            $contrat->bien->update(['statut' => 'disponible']);

            return redirect()
                ->route('contrats.show', $contrat)
                ->with('success', 'Contrat résilié avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la résiliation : ' . $e->getMessage());
        }
    }

    /**
     * Renouveler un contrat
     */
    public function renouveler(Request $request, Contrat $contrat)
    {
        $request->validate([
            'nouvelle_date_fin' => 'required|date|after:' . $contrat->date_fin,
            'nouveau_loyer_hc' => 'nullable|numeric|min:0',
            'nouvelles_charges' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculer la nouvelle durée
            $nouvelleDateFin = $request->nouvelle_date_fin;
            $duree = now()->parse($contrat->date_debut)->diffInMonths($nouvelleDateFin);

            // Mettre à jour le contrat
            $contrat->update([
                'date_fin' => $nouvelleDateFin,
                'duree_mois' => $duree,
                'loyer_hc' => $request->nouveau_loyer_hc ?? $contrat->loyer_hc,
                'charges' => $request->nouvelles_charges ?? $contrat->charges,
                'loyer_cc' => ($request->nouveau_loyer_hc ?? $contrat->loyer_hc) + ($request->nouvelles_charges ?? $contrat->charges),
                'statut' => 'actif',
            ]);

            DB::commit();

            return redirect()
                ->route('contrats.show', $contrat)
                ->with('success', 'Contrat renouvelé avec succès jusqu\'au ' . $nouvelleDateFin);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Erreur lors du renouvellement : ' . $e->getMessage());
        }
    }

    /**
     * Générer un document pour ce contrat
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Contrat $contrat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function genererDocument(Request $request, Contrat $contrat)
    {
        $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'format' => 'required|in:pdf,docx',
        ]);

        try {
            $template = DocumentTemplate::findOrFail($request->template_id);
            
            // Générer le document via le service
            $document = $this->documentGenerator->generate($template, $contrat, $request->format);

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Document généré avec succès !');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    /**
     * Générer une référence unique
     */
    private function generateReference()
    {
        $year = date('Y');
        $lastContrat = Contrat::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastContrat ? (intval(substr($lastContrat->reference, -4)) + 1) : 1;

        return 'CONT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Générer automatiquement les documents initiaux (bail, état des lieux)
     * 
     * @param \App\Models\Contrat $contrat
     * @return void
     */
    private function genererDocumentsInitiaux(Contrat $contrat): void
    {
        // Trouver le modèle de bail correspondant au type
        $templateBail = DocumentTemplate::where('type', 'bail_' . $contrat->type_bail)
            ->where('actif', true)
            ->where('is_default', true)
            ->first();

        if ($templateBail instanceof DocumentTemplate) {
            $this->documentGenerator->generate($templateBail, $contrat, 'pdf');
        }

        // Générer l'état des lieux d'entrée
        $templateEdl = DocumentTemplate::where('type', 'etat_lieux_entree')
            ->where('actif', true)
            ->where('is_default', true)
            ->first();

        if ($templateEdl instanceof DocumentTemplate) {
            $this->documentGenerator->generate($templateEdl, $contrat, 'pdf');
        }
    }
}