<?php

namespace App\Services;

use App\Models\EtatDesLieux;
use App\Models\Document;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class EtatDesLieuxPdfService
{
    /**
     * Générer le PDF d'un état des lieux
     * 
     * @param EtatDesLieux $etatDesLieux
     * @param int|null $userId
     * @return Document
     */
    public function generate(EtatDesLieux $etatDesLieux, ?int $userId = null): Document
    {
        // Charger toutes les relations nécessaires
        $etatDesLieux->load([
            'bien.proprietaire',
            'contrat.locataires',
            'pieces.elements'
        ]);

        // Générer le HTML depuis la vue Blade
        $html = View::make('etats-des-lieux.pdf-template', [
            'edl' => $etatDesLieux
        ])->render();

        // Configurer Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('dpi', 150);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Générer le nom de fichier
        $fileName = $this->generateFileName($etatDesLieux);
        $filePath = 'edl/pdf/' . $fileName;

        // Sauvegarder le PDF
        Storage::put($filePath, $dompdf->output());

        // Créer l'entrée dans la table documents
        $document = Document::create([
            'nom' => $this->generateDocumentName($etatDesLieux),
            'type' => $etatDesLieux->type === 'entree' ? 'etat_lieux_entree' : 'etat_lieux_sortie',
            'format' => 'pdf',
            'file_type' => 'application/pdf', // ✅ AJOUTÉ
            'file_path' => $filePath,
            'file_size' => Storage::size($filePath),
            'contrat_id' => $etatDesLieux->contrat_id,
            'bien_id' => $etatDesLieux->bien_id,
            'template_id' => null,
            'generated_by' => $userId,
            'is_uploaded' => false,
            'statut' => 'genere', // ✅ AJOUTÉ si nécessaire
        ]);

        // Associer le document à l'état des lieux
        $etatDesLieux->update(['document_id' => $document->id]);

        // Logger la génération
        $document->logs()->create([
            'action' => 'created',
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'details' => 'État des lieux généré en PDF',
        ]);

        return $document;
    }

    /**
     * Générer le nom du fichier PDF
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return string
     */
    private function generateFileName(EtatDesLieux $etatDesLieux): string
    {
        $type = $etatDesLieux->type === 'entree' ? 'EDL-Entree' : 'EDL-Sortie';
        $reference = str_replace(['/', '\\', ' '], '-', $etatDesLieux->bien->reference);
        $date = $etatDesLieux->date_etat->format('Ymd');
        $timestamp = now()->format('His');
        
        return "{$type}-{$reference}-{$date}-{$timestamp}.pdf";
    }

    /**
     * Générer le nom du document
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return string
     */
    private function generateDocumentName(EtatDesLieux $etatDesLieux): string
    {
        $type = $etatDesLieux->type === 'entree' ? 'État des lieux d\'entrée' : 'État des lieux de sortie';
        $reference = $etatDesLieux->bien->reference;
        $date = $etatDesLieux->date_etat->format('d/m/Y');
        
        return "{$type} - {$reference} - {$date}";
    }

    /**
     * Régénérer un PDF existant
     * 
     * @param EtatDesLieux $etatDesLieux
     * @param int|null $userId
     * @return Document
     */
    public function regenerate(EtatDesLieux $etatDesLieux, ?int $userId = null): Document
    {
        // Supprimer l'ancien document si existant
        if ($etatDesLieux->document_id) {
            $oldDocument = Document::find($etatDesLieux->document_id);
            if ($oldDocument) {
                // Supprimer le fichier
                if (Storage::exists($oldDocument->file_path)) {
                    Storage::delete($oldDocument->file_path);
                }
                // Supprimer l'entrée
                $oldDocument->forceDelete();
            }
        }

        // Générer un nouveau document
        return $this->generate($etatDesLieux, $userId);
    }

    /**
     * Télécharger le PDF
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(EtatDesLieux $etatDesLieux)
    {
        if (!$etatDesLieux->document_id) {
            throw new \Exception('Aucun document PDF généré pour cet état des lieux');
        }

        $document = $etatDesLieux->document;
        
        if (!Storage::exists($document->file_path)) {
            throw new \Exception('Fichier PDF introuvable');
        }

        // Logger le téléchargement
        $document->logs()->create([
            'action' => 'downloaded',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        $fileName = $this->generateFileName($etatDesLieux);
        
        return Storage::download($document->file_path, $fileName);
    }

    /**
     * Prévisualiser le HTML (avant génération PDF)
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return string
     */
    public function previewHtml(EtatDesLieux $etatDesLieux): string
    {
        $etatDesLieux->load([
            'bien.proprietaire',
            'contrat.locataires',
            'pieces.elements'
        ]);

        return View::make('etats-des-lieux.pdf-template', [
            'edl' => $etatDesLieux
        ])->render();
    }

    /**
     * Vérifier si un état des lieux peut être généré en PDF
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return bool
     */
    public function canGenerate(EtatDesLieux $etatDesLieux): bool
    {
        // Doit avoir au moins une pièce
        if ($etatDesLieux->pieces()->count() === 0) {
            return false;
        }

        // Recommandé : être marqué comme terminé
        // (mais pas obligatoire, on peut générer en brouillon)
        
        return true;
    }

    /**
     * Obtenir les statistiques d'un état des lieux pour le PDF
     * 
     * @param EtatDesLieux $etatDesLieux
     * @return array
     */
    public function getStatistics(EtatDesLieux $etatDesLieux): array
    {
        return [
            'total_pieces' => $etatDesLieux->pieces()->count(),
            'total_elements' => $etatDesLieux->pieces()
                ->withCount('elements')
                ->get()
                ->sum('elements_count'),
            'total_photos' => $etatDesLieux->getTotalPhotos(),
            'elements_remplis' => $etatDesLieux->pieces()
                ->with('elements')
                ->get()
                ->flatMap->elements
                ->filter->isComplete()
                ->count(),
        ];
    }
}