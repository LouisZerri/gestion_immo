<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Contrat;
use App\Models\Bien;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $generatorService;
    
    public function __construct(DocumentGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }
    
    /**
     * Afficher la liste des documents avec FILTRES AVANCÉS
     */
    public function index(Request $request)
    {
        $query = Document::with(['contrat.bien', 'contrat.locataires', 'template', 'bien', 'locataire', 'proprietaire']);

        // Filtre par rôle utilisateur
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        if ($user->isLocataire()) {
            // TODO: Filtrer par locataire connecté quand relation User->Locataire sera créée
            // $query->where('locataire_id', $user->locataire_id);
        } elseif ($user->isProprietaire()) {
            // TODO: Filtrer par propriétaire connecté quand relation User->Proprietaire sera créée
            // $query->where('proprietaire_id', $user->proprietaire_id);
        }

        // ✅ FILTRE : Type de document
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ✅ FILTRE : Format (PDF/Word)
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // ✅ FILTRE : Bien
        if ($request->filled('bien_id')) {
            $query->where('bien_id', $request->bien_id);
        }

        // ✅ FILTRE : Locataire
        if ($request->filled('locataire_id')) {
            $query->where('locataire_id', $request->locataire_id);
        }

        // ✅ FILTRE : Propriétaire
        if ($request->filled('proprietaire_id')) {
            $query->where('proprietaire_id', $request->proprietaire_id);
        }

        // ✅ FILTRE : Contrat
        if ($request->filled('contrat_id')) {
            $query->where('contrat_id', $request->contrat_id);
        }

        // ✅ FILTRE : Date (période)
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // ✅ FILTRE : Partagé ou non
        if ($request->filled('partage')) {
            if ($request->partage === 'oui') {
                $query->where('is_shared', true);
            } elseif ($request->partage === 'non') {
                $query->where('is_shared', false);
            }
        }

        // ✅ FILTRE : Recherche globale
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Tri par date (plus récent en premier)
        $documents = $query->latest()->paginate(20)->withQueryString();

        // Données pour les filtres
        $types = [
            'bail_vide' => 'Bail vide',
            'bail_meuble' => 'Bail meublé',
            'bail_commercial' => 'Bail commercial',
            'bail_parking' => 'Bail parking',
            'etat_lieux_entree' => 'État des lieux entrée',
            'etat_lieux_sortie' => 'État des lieux sortie',
            'quittance_loyer' => 'Quittance de loyer',
            'avis_echeance' => 'Avis d\'échéance',
            'mandat_gestion' => 'Mandat de gestion',
            'inventaire' => 'Inventaire',
            'attestation_loyer' => 'Attestation de loyer',
            'document_externe' => 'Document externe',
            'autre' => 'Autre',
        ];

        $formats = [
            'pdf' => 'PDF',
            'docx' => 'Word (DOCX)',
        ];

        $biens = \App\Models\Bien::orderBy('adresse')->get();
        $locataires = \App\Models\Locataire::orderBy('nom')->get();
        $proprietaires = \App\Models\Proprietaire::orderBy('nom')->get();
        $contrats = \App\Models\Contrat::with('bien')->where('statut', 'actif')->get();

        return view('documents.index', compact(
            'documents',
            'types',
            'formats',
            'biens',
            'locataires',
            'proprietaires',
            'contrats'
        ));
    }
    
    /**
     * Interface de génération
     */
    public function create(Request $request)
    {
        // Si un contrat est pré-sélectionné
        $contratId = $request->get('contrat_id');
        $selectedContrat = $contratId ? Contrat::with(['bien', 'locataires'])->find($contratId) : null;
        
        // ✅ Si un bien est pré-sélectionné, filtrer les contrats par ce bien
        $bienId = $request->get('bien');
        $selectedBien = $bienId ? Bien::find($bienId) : null;
        
        if ($bienId) {
            // Contrats du bien spécifique uniquement
            $contrats = Contrat::with(['bien', 'locataires'])
                ->where('bien_id', $bienId)
                ->where('statut', 'actif')
                ->orderBy('reference')
                ->get();
        } else {
            // Tous les contrats actifs
            $contrats = Contrat::with(['bien', 'locataires'])
                ->where('statut', 'actif')
                ->orderBy('reference')
                ->get();
        }
        
        // Liste des modèles actifs
        $templates = DocumentTemplate::where('actif', true)
            ->orderBy('type')
            ->orderBy('nom')
            ->get()
            ->groupBy('type');
        
        return view('documents.create', compact('templates', 'contrats', 'selectedContrat', 'selectedBien'));
    }
    
    /**
     * Prévisualisation avec données réelles
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'contrat_id' => 'required|exists:contrats,id',
        ]);
        
        $template = DocumentTemplate::findOrFail($request->template_id);
        
        // ✅ Charger les garants via les locataires
        $contrat = Contrat::with([
            'bien.proprietaire', 
            'locataires.garants'
        ])->findOrFail($request->contrat_id);
        
        // Utiliser le service pour collecter les données et remplacer les balises
        $service = app(DocumentGeneratorService::class);
        $reflection = new \ReflectionClass($service);
        
        // Collecter les données
        $collectDataMethod = $reflection->getMethod('collectData');
        $collectDataMethod->setAccessible(true);
        $data = $collectDataMethod->invoke($service, $contrat);
        
        // Remplacer les balises
        $replaceTagsMethod = $reflection->getMethod('replaceAllTags');
        $replaceTagsMethod->setAccessible(true);
        $content = $replaceTagsMethod->invoke($service, $template->contenu, $data);
        
        return view('documents.preview-real', compact('template', 'contrat', 'content'));
    }
    
    /**
     * Générer le document
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'contrat_id' => 'required|exists:contrats,id',
            'format' => 'required|in:pdf,docx',
        ]);
        
        $template = DocumentTemplate::findOrFail($request->template_id);
        
        // ✅ NOUVEAU : Redirection automatique vers le module État des lieux
        if (in_array($template->type, ['etat_lieux_entree', 'etat_lieux_sortie'])) {
            $type = $template->type === 'etat_lieux_entree' ? 'entree' : 'sortie';
            
            // Récupérer le contrat pour obtenir le bien_id
            $contrat = Contrat::findOrFail($request->contrat_id);
            
            return redirect()
                ->route('etats-des-lieux.create', [
                    'bien' => $contrat->bien_id,
                    'contrat' => $request->contrat_id,
                    'type' => $type
                ])
                ->with('info', '✨ Vous avez été redirigé vers le module État des lieux pour créer un état des lieux complet avec photos et détails pièce par pièce.');
        }
        
        // Charger les garants via les locataires
        $contrat = Contrat::with([
            'bien.proprietaire', 
            'locataires.garants'
        ])->findOrFail($request->contrat_id);
        
        try {
            // Générer le document
            $document = $this->generatorService->generate(
                $template,
                $contrat,
                $request->format,
                Auth::id() ?? null
            );
            
            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Document généré avec succès !');
                
        } catch (\Exception $e) {
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }
    
    /**
     * Afficher un document
     */
    public function show(Document $document)
    {
        $document->load(['template', 'contrat.bien', 'contrat.locataires', 'logs.user']);
        
        return view('documents.show', compact('document'));
    }
    
    /**
     * Télécharger un document
     */
    public function download(Document $document)
    {
        // Vérifier si le fichier existe
        if (!Storage::exists($document->file_path)) {
            abort(404, 'Fichier introuvable : ' . $document->file_path);
        }
        
        // Nettoyer le nom de fichier (remplacer / et \ par -)
        $fileName = str_replace(['/', '\\'], '-', $document->nom) . '.' . $document->format;
  
        return Storage::download($document->file_path, $fileName);
    }
    
    /**
     * Supprimer un document
     */
    public function destroy(Document $document)
    {
        // Supprimer le fichier
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }
        
        // Soft delete du document
        $document->delete();
        
        return redirect()
            ->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }
    
    /**
     * Regénérer un document
     */
    public function regenerate(Document $document, Request $request)
    {
        $request->validate([
            'format' => 'sometimes|in:pdf,docx',
        ]);
        
        $format = $request->get('format', $document->format);
        
        try {
            // Supprimer l'ancien fichier
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
            
            // Regénérer
            $newDocument = $this->generatorService->generate(
                $document->template,
                $document->contrat,
                $format,
                Auth::id() ?? null
            );
            
            // Supprimer l'ancienne entrée
            $document->forceDelete();
            
            return redirect()
                ->route('documents.show', $newDocument)
                ->with('success', 'Document régénéré avec succès !');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la régénération : ' . $e->getMessage());
        }
    }
    
    /**
     * Documents par contrat
     */
    public function byContrat(Contrat $contrat)
    {
        $documents = $contrat->documents()
            ->with('template')
            ->latest()
            ->get();
        
        return view('documents.by-contrat', compact('contrat', 'documents'));
    }
}