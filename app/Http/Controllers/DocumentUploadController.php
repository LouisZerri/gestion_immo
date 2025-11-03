<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Document;
use App\Models\Locataire;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentUploadController extends Controller
{
    /**
     * Afficher le formulaire d'upload
     */
    public function create()
    {
        $contrats = Contrat::with(['bien', 'locataires'])
            ->where('statut', 'actif')
            ->latest()
            ->get();

        $biens = Bien::orderBy('adresse')->get();
        $locataires = Locataire::orderBy('nom')->get();
        $proprietaires = Proprietaire::orderBy('nom')->get();

        return view('documents.upload', compact('contrats', 'biens', 'locataires', 'proprietaires'));
    }

    /**
     * Traiter l'upload
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrat_id' => 'nullable|exists:contrats,id',
            'bien_id' => 'nullable|exists:biens,id',
            'locataire_id' => 'nullable|exists:locataires,id',
            'proprietaire_id' => 'nullable|exists:proprietaires,id',
            'nom' => 'required|string|max:255',
            'type' => 'required|in:bail_vide,bail_meuble,bail_commercial,bail_parking,etat_lieux_entree,etat_lieux_sortie,quittance_loyer,avis_echeance,mandat_gestion,inventaire,attestation_loyer,document_externe,autre',
            'fichier' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:10240', // 10 Mo
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5 Mo par photo
            'notes' => 'nullable|string|max:1000',
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier à uploader.',
            'fichier.mimes' => 'Formats acceptés : PDF, Word (.doc, .docx), Excel (.xls, .xlsx), Images (JPG, PNG, GIF).',
            'fichier.max' => 'La taille maximale du fichier est de 10 Mo.',
            'photos.*.image' => 'Les photos doivent être des images.',
            'photos.*.max' => 'Taille maximale par photo : 5 Mo.',
        ]);

        // Upload du fichier principal
        $file = $request->file('fichier');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $path = $file->storeAs('documents', $filename);

        // Déterminer le format
        $format = 'pdf';
        if (in_array($extension, ['doc', 'docx'])) {
            $format = 'docx';
        }

        // Créer le document
        $document = Document::create([
            'contrat_id' => $validated['contrat_id'],
            'bien_id' => $validated['bien_id'],
            'locataire_id' => $validated['locataire_id'],
            'proprietaire_id' => $validated['proprietaire_id'],
            'nom' => $validated['nom'],
            'type' => $validated['type'],
            'format' => $format,
            'file_path' => $path,
            'file_type' => $extension,
            'file_size' => $file->getSize(),
            'is_uploaded' => true,
            'original_filename' => $originalName,
            'statut' => 'genere',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Upload des photos (pour état des lieux)
        if ($request->hasFile('photos')) {
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photoFilename = time() . '_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('documents/photos', $photoFilename);
                $photoPaths[] = $photoPath;
            }
            $document->addPhotos($photoPaths);
        }

        // Log de l'action
        $document->logAction('uploaded', Auth::id(), "Document externe uploadé : {$originalName}");

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document uploadé avec succès !');
    }
}