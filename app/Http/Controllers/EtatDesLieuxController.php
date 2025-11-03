<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Models\EdlPiece;
use App\Models\EdlElement;
use App\Models\Bien;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EtatDesLieuxController extends Controller
{
    /**
     * Liste des états des lieux
     */
    public function index(Request $request)
    {
        $query = EtatDesLieux::with(['bien', 'contrat']);

        // Filtres
        if ($request->filled('bien_id')) {
            $query->where('bien_id', $request->bien_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $etatsDesLieux = $query->latest()->paginate(15);

        // Données pour les filtres
        $biens = Bien::orderBy('reference')->get();

        return view('etats-des-lieux.index', compact('etatsDesLieux', 'biens'));
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        // Si un bien est pré-sélectionné
        $bienId = $request->get('bien');
        $selectedBien = $bienId ? Bien::find($bienId) : null;

        // Si un contrat est pré-sélectionné
        $contratId = $request->get('contrat');
        $selectedContrat = $contratId ? Contrat::with('bien')->find($contratId) : null;

        // Si contrat, on récupère le bien associé
        if ($selectedContrat) {
            $selectedBien = $selectedContrat->bien;
        }

        // Liste des biens
        $biens = Bien::where('statut', '!=', 'vendu')->orderBy('reference')->get();

        // Liste des contrats actifs
        $contrats = Contrat::where('statut', 'actif')->with('bien')->orderBy('reference')->get();

        return view('etats-des-lieux.create', compact('biens', 'contrats', 'selectedBien', 'selectedContrat'));
    }

    /**
     * Initialiser un nouvel état des lieux
     */
    public function store(Request $request)
    {
        $request->validate([
            'bien_id' => 'required|exists:biens,id',
            'contrat_id' => 'nullable|exists:contrats,id',
            'type' => 'required|in:entree,sortie',
            'date_etat' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Créer l'état des lieux
            $edl = EtatDesLieux::create([
                'bien_id' => $request->bien_id,
                'contrat_id' => $request->contrat_id,
                'type' => $request->type,
                'date_etat' => $request->date_etat,
                'statut' => 'brouillon',
            ]);

            // Créer les pièces par défaut avec leurs éléments
            $this->creerPiecesParDefaut($edl);

            DB::commit();

            return redirect()
                ->route('etats-des-lieux.edit', $edl)
                ->with('success', 'État des lieux créé ! Remplissez maintenant les détails pièce par pièce.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Formulaire d'édition (remplissage)
     */
    public function edit(EtatDesLieux $etatDesLieux)
    {
        // Charger TOUTES les relations nécessaires
        $etatDesLieux->load(['bien.proprietaire', 'contrat.locataires', 'pieces.elements']);

        return view('etats-des-lieux.edit', compact('etatDesLieux'));
    }

    /**
     * Mise à jour de l'état des lieux
     */
    public function update(Request $request, EtatDesLieux $etatDesLieux)
    {
        $request->validate([
            'date_etat' => 'required|date',
            'observations_generales' => 'nullable|string',
            'compteurs_eau' => 'nullable|array',
            'compteurs_gaz' => 'nullable|array',
            'compteurs_electricite' => 'nullable|array',
            'chauffage' => 'nullable|array',
            'eau_chaude' => 'nullable|array',
            'cles' => 'nullable|array',
            'autres_amenagements' => 'nullable|array',
            'pieces' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Mise à jour de l'état des lieux
            $etatDesLieux->update([
                'date_etat' => $request->date_etat,
                'observations_generales' => $request->observations_generales,
                'compteurs_eau' => $request->compteurs_eau,
                'compteurs_gaz' => $request->compteurs_gaz,
                'compteurs_electricite' => $request->compteurs_electricite,
                'chauffage' => $request->chauffage,
                'eau_chaude' => $request->eau_chaude,
                'cles' => $request->cles,
                'autres_amenagements' => $request->autres_amenagements,
            ]);

            // Mise à jour des pièces et éléments
            if ($request->has('pieces')) {
                foreach ($request->pieces as $pieceId => $pieceData) {
                    $piece = EdlPiece::find($pieceId);
                    if ($piece) {
                        $piece->update([
                            'commentaires_piece' => $pieceData['commentaires_piece'] ?? null,
                        ]);

                        // Mise à jour des éléments
                        if (isset($pieceData['elements'])) {
                            foreach ($pieceData['elements'] as $elementId => $elementData) {
                                $element = EdlElement::find($elementId);
                                if ($element) {
                                    $element->update([
                                        'nature' => $elementData['nature'] ?? null,
                                        'etat_usure' => $elementData['etat_usure'] ?? null,
                                        'fonctionnement' => $elementData['fonctionnement'] ?? null,
                                        'commentaires' => $elementData['commentaires'] ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('etats-des-lieux.edit', $etatDesLieux)
                ->with('success', 'État des lieux enregistré avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Upload de photos pour une pièce
     */
    public function uploadPhotos(Request $request, EdlPiece $piece)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            if ($request->hasFile('photos')) {
                $uploadedPhotos = [];

                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('edl/pieces/' . $piece->id, 'public');
                    $uploadedPhotos[] = $path;
                }

                // Ajouter les photos
                $currentPhotos = $piece->photos ?? [];
                $piece->update(['photos' => array_merge($currentPhotos, $uploadedPhotos)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Photos ajoutées avec succès',
                'photos' => $piece->photos,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprimer une photo
     */
    public function deletePhoto(Request $request, EdlPiece $piece)
    {
        $request->validate([
            'photo_path' => 'required|string',
        ]);

        try {
            $photoPath = $request->photo_path;

            // Supprimer du storage
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            // Retirer du JSON
            $piece->removePhoto($photoPath);

            return response()->json([
                'success' => true,
                'message' => 'Photo supprimée',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ajouter une nouvelle pièce
     */
    public function ajouterPiece(Request $request, EtatDesLieux $etatDesLieux)
    {
        $request->validate([
            'nom_piece' => 'required|string|max:255',
        ]);

        try {
            $ordre = $etatDesLieux->pieces()->max('ordre') + 1;

            $piece = EdlPiece::create([
                'etat_des_lieux_id' => $etatDesLieux->id,
                'nom_piece' => $request->nom_piece,
                'ordre' => $ordre,
            ]);

            // Ajouter les éléments par défaut
            $this->creerElementsParDefaut($piece);

            return redirect()
                ->route('etats-des-lieux.edit', $etatDesLieux)
                ->with('success', 'Pièce ajoutée avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Ajouter un nouvel élément à une pièce
     */
    public function ajouterElement(Request $request, EdlPiece $piece)
    {
        $request->validate([
            'element' => 'required|string|max:255',
        ]);

        try {
            $ordre = $piece->elements()->max('ordre') + 1;

            EdlElement::create([
                'edl_piece_id' => $piece->id,
                'element' => $request->element,
                'ordre' => $ordre,
            ]);

            return redirect()
                ->route('etats-des-lieux.edit', $piece->etatDesLieux)
                ->with('success', 'Élément ajouté avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Marquer comme terminé
     */
    public function terminer(EtatDesLieux $etatDesLieux)
    {
        try {
            $etatDesLieux->marquerTermine();

            return redirect()
                ->route('etats-des-lieux.show', $etatDesLieux)
                ->with('success', 'État des lieux marqué comme terminé !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Afficher l'état des lieux
     */
    public function show(EtatDesLieux $etatDesLieux)
    {
        $etatDesLieux->load(['bien', 'contrat', 'pieces.elements']);

        return view('etats-des-lieux.show', compact('etatDesLieux'));
    }

    /**
     * Générer le PDF
     */
    public function generatePdf(EtatDesLieux $etatDesLieux)
    {
        try {
            $service = app(\App\Services\EtatDesLieuxPdfService::class);
            
            // Vérifier si on peut générer
            if (!$service->canGenerate($etatDesLieux)) {
                return back()->with('error', 'Cet état des lieux ne peut pas être généré (aucune pièce).');
            }
            
            // Générer ou régénérer le PDF
            if ($etatDesLieux->document_id) {
                $document = $service->regenerate($etatDesLieux, Auth::id());
                $message = 'PDF régénéré avec succès !';
            } else {
                $document = $service->generate($etatDesLieux, Auth::id());
                $message = 'PDF généré avec succès !';
            }
            
            // Télécharger automatiquement
            return $service->download($etatDesLieux);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un état des lieux
     */
    public function destroy(EtatDesLieux $etatDesLieux)
    {
        try {
            // Supprimer les photos
            foreach ($etatDesLieux->pieces as $piece) {
                if ($piece->photos) {
                    foreach ($piece->photos as $photo) {
                        if (Storage::disk('public')->exists($photo)) {
                            Storage::disk('public')->delete($photo);
                        }
                    }
                }
            }

            $etatDesLieux->delete();

            return redirect()
                ->route('etats-des-lieux.index')
                ->with('success', 'État des lieux supprimé avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Créer les pièces par défaut
     */
    private function creerPiecesParDefaut(EtatDesLieux $edl): void
    {
        $piecesParDefaut = [
            'ENTREE',
            'SEJOUR',
            'CUISINE',
            'CHAMBRE 1',
            'SALLE DE BAIN 1',
            'WC 1',
        ];

        $ordre = 1;
        foreach ($piecesParDefaut as $nomPiece) {
            $piece = EdlPiece::create([
                'etat_des_lieux_id' => $edl->id,
                'nom_piece' => $nomPiece,
                'ordre' => $ordre++,
            ]);

            $this->creerElementsParDefaut($piece);
        }
    }

    /**
     * Créer les éléments par défaut pour une pièce
     */
    private function creerElementsParDefaut(EdlPiece $piece): void
    {
        $elementsCommuns = [
            'MURS A',
            'MURS B',
            'MURS C',
            'MURS D',
            'SOL',
            'PLAFOND',
            'PORTE(S)',
            'PLINTHES',
            'FENETRE(S)',
            'VOLETS, STORES',
            'CHAUFFAGE',
            'VENTILATION',
            'INTERRUPTEURS',
            'PRISES',
            'ECLAIRAGE',
        ];

        // Éléments spécifiques par type de pièce
        $elementsSpecifiques = [];

        if (str_contains(strtoupper($piece->nom_piece), 'CUISINE')) {
            $elementsSpecifiques = [
                'ELEMENTS BAS',
                'ELEMENTS HAUT',
                'PLAN DE TRAVAIL',
                'EVIER',
                'MEUBLE SOUS EVIER',
                'GRILLE(S) DE VENTILATION',
            ];
        } elseif (str_contains(strtoupper($piece->nom_piece), 'SALLE DE BAIN')) {
            $elementsSpecifiques = [
                'FAÏENCE',
                'LAVABO(S)',
                'DOUCHE, BAIGNOIRE',
                'WC',
                'ABATTANT',
                'PLOMBERIE',
                'MECANISME CHASSE D\'EAU',
                'JOINTS',
                'GRILLE(S) DE VENTILATION',
            ];
        } elseif (str_contains(strtoupper($piece->nom_piece), 'WC')) {
            $elementsSpecifiques = [
                'FAÏENCE',
                'LAVABO(S)',
                'WC',
                'ABATTANT',
                'PLOMBERIE',
                'MECANISME CHASSE D\'EAU',
                'JOINTS',
                'GRILLE(S) DE VENTILATION',
            ];
        } elseif (str_contains(strtoupper($piece->nom_piece), 'CHAMBRE') || 
                  str_contains(strtoupper($piece->nom_piece), 'SEJOUR')) {
            $elementsSpecifiques = ['PLACARD'];
        }

        $elements = array_merge($elementsCommuns, $elementsSpecifiques);

        $ordre = 1;
        foreach ($elements as $element) {
            EdlElement::create([
                'edl_piece_id' => $piece->id,
                'element' => $element,
                'ordre' => $ordre++,
            ]);
        }
    }
}