<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Contrat;
use App\Models\DocumentLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentGeneratorService
{
    /**
     * Générer un document PDF à partir d'un modèle et d'un contrat
     */
    public function generate(DocumentTemplate $template, Contrat $contrat, string $format = 'pdf', ?int $userId = null): Document
    {
        // ✅ Forcer le format PDF
        $format = 'pdf';

        // Récupérer toutes les données nécessaires
        $data = $this->collectData($contrat);

        // Remplacer les balises dans le contenu
        $content = $this->replaceAllTags($template->contenu, $data);

        // Ajouter le logo si présent
        if ($template->logo_path) {
            $content = $this->insertLogo($content, $template->logo_path);
        }

        // Ajouter la signature si présente
        if ($template->signature_path) {
            $content = $this->insertSignature($content, $template->signature_path);
        }

        // Ajouter le footer si présent
        if ($template->footer_text) {
            $content = $this->insertFooter($content, $template->footer_text);
        }

        // Générer le PDF
        $filePath = $this->generatePDF($content, $template, $contrat);
        $fileType = 'application/pdf';
        $fileSize = Storage::exists($filePath) ? Storage::size($filePath) : 0;

        // Créer l'enregistrement du document
        $document = Document::create([
            'template_id' => $template->id,
            'contrat_id' => $contrat->id,
            'bien_id' => $contrat->bien_id,
            'nom' => $this->generateDocumentName($template, $contrat),
            'type' => $template->type,
            'format' => 'pdf',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        // Logger la génération
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => $userId,
            'action' => 'genere',
            'details' => json_encode([
                'template' => $template->nom,
                'format' => 'pdf',
                'contrat' => $contrat->reference,
            ]),
            'ip_address' => request()->ip(),
        ]);

        return $document;
    }

    /**
     * Collecter toutes les données depuis le contrat
     */
    private function collectData(Contrat $contrat): array
    {
        $bien = $contrat->bien;
        $proprietaire = $bien->proprietaire;
        $locataires = $contrat->locataires;

        // Récupérer les garants via les locataires
        $garants = collect();
        foreach ($locataires as $locataire) {
            if ($locataire->garants) {
                $garants = $garants->merge($locataire->garants);
            }
        }

        // Données de base
        $data = [
            // Bien
            'Bien_Reference' => $bien->reference ?? '',
            'Bien_Adresse' => $bien->adresse ?? '',
            'Bien_CodePostal' => $bien->code_postal ?? '',
            'Bien_Ville' => $bien->ville ?? '',
            'Bien_Pays' => $bien->pays ?? 'France',
            'Bien_Type' => $bien->type_libelle ?? '',
            'Bien_Surface' => $bien->surface ? number_format($bien->surface, 2, ',', ' ') : '',
            'Bien_NombrePieces' => $bien->nombre_pieces ?? '',
            'Bien_Etage' => $bien->etage ?? '',
            'Bien_DPE' => $bien->dpe ?? '',

            // Propriétaire
            'Proprietaire_Nom' => $proprietaire->nom ?? '',
            'Proprietaire_Prenom' => $proprietaire->prenom ?? '',
            'Proprietaire_NomComplet' => $proprietaire->nom_complet ?? '',
            'Proprietaire_Adresse' => $proprietaire->adresse ?? '',
            'Proprietaire_CodePostal' => $proprietaire->code_postal ?? '',
            'Proprietaire_Ville' => $proprietaire->ville ?? '',
            'Proprietaire_Email' => $proprietaire->email ?? '',
            'Proprietaire_Telephone' => $proprietaire->telephone ?? '',
            'Proprietaire_IBAN' => $proprietaire->iban ?? '',

            // Contrat
            'Contrat_Reference' => $contrat->reference ?? '',
            'Contrat_TypeBail' => $contrat->type_bail_libelle ?? '',
            'Contrat_DateDebut' => $contrat->date_debut ? $contrat->date_debut->format('d/m/Y') : '',
            'Contrat_DateFin' => $contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : '',
            'Contrat_DureeMois' => $contrat->duree_mois ?? '',
            'Contrat_LoyerHC' => $contrat->loyer_hc ? number_format($contrat->loyer_hc, 2, ',', ' ') . ' €' : '',
            'Contrat_Charges' => $contrat->charges ? number_format($contrat->charges, 2, ',', ' ') . ' €' : '',
            'Contrat_LoyerCC' => $contrat->loyer_cc ? number_format($contrat->loyer_cc, 2, ',', ' ') . ' €' : '',
            'Contrat_DepotGarantie' => $contrat->depot_garantie ? number_format($contrat->depot_garantie, 2, ',', ' ') . ' €' : '',
            'Contrat_JourPaiement' => $contrat->jour_paiement ?? '',
            'Contrat_DateSignature' => $contrat->date_signature ? $contrat->date_signature->format('d/m/Y') : '',

            // Dates
            'Date_Aujourd_hui' => now()->format('d/m/Y'),
            'Date_Generation' => now()->format('d/m/Y H:i'),
        ];

        // Premier locataire (pour compatibilité avec les balises simples)
        if ($locataires->isNotEmpty()) {
            $premierLocataire = $locataires->first();
            $data = array_merge($data, [
                'Locataire_Nom' => $premierLocataire->nom ?? '',
                'Locataire_Prenom' => $premierLocataire->prenom ?? '',
                'Locataire_NomComplet' => $premierLocataire->nom_complet ?? '',
                'Locataire_DateNaissance' => $premierLocataire->date_naissance ? $premierLocataire->date_naissance->format('d/m/Y') : '',
                'Locataire_LieuNaissance' => $premierLocataire->lieu_naissance ?? '',
                'Locataire_Adresse' => $premierLocataire->adresse ?? '',
                'Locataire_CodePostal' => $premierLocataire->code_postal ?? '',
                'Locataire_Ville' => $premierLocataire->ville ?? '',
                'Locataire_Email' => $premierLocataire->email ?? '',
                'Locataire_Telephone' => $premierLocataire->telephone ?? '',
                'Locataire_Profession' => $premierLocataire->profession ?? '',
            ]);
        }

        // Stocker les collections pour les boucles
        $data['_locataires'] = $locataires;
        $data['_garants'] = $garants;

        return $data;
    }

    /**
     * Remplacer toutes les balises dans le contenu
     */
    private function replaceAllTags(string $content, array $data): string
    {
        // D'abord gérer les boucles
        $content = $this->processLoops($content, $data);

        // Ensuite remplacer les balises simples
        foreach ($data as $key => $value) {
            // Ignorer les collections (préfixées par _)
            if (str_starts_with($key, '_')) {
                continue;
            }

            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    /**
     * Traiter les boucles (LocataireBlock, GarantBlock)
     */
    private function processLoops(string $content, array $data): string
    {
        // Traiter les boucles de locataires
        $content = $this->processLocataireLoop($content, $data['_locataires'] ?? collect());

        // Traiter les boucles de garants
        $content = $this->processGarantLoop($content, $data['_garants'] ?? collect());

        return $content;
    }

    /**
     * Traiter la boucle des locataires
     */
    private function processLocataireLoop(string $content, $locataires): string
    {
        // Trouver le bloc de boucle
        $pattern = '/{{LocataireBlockStart}}(.*?){{LocataireBlockEnd}}/s';

        return preg_replace_callback($pattern, function ($matches) use ($locataires) {
            $blockTemplate = $matches[1];
            $result = '';

            foreach ($locataires as $locataire) {
                $blockContent = $blockTemplate;

                // Remplacer les balises du locataire
                $blockContent = str_replace('{{Locataire_Nom}}', $locataire->nom ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Prenom}}', $locataire->prenom ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_NomComplet}}', $locataire->nom_complet ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_DateNaissance}}', $locataire->date_naissance ? $locataire->date_naissance->format('d/m/Y') : '', $blockContent);
                $blockContent = str_replace('{{Locataire_LieuNaissance}}', $locataire->lieu_naissance ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Adresse}}', $locataire->adresse ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_CodePostal}}', $locataire->code_postal ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Ville}}', $locataire->ville ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Email}}', $locataire->email ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Telephone}}', $locataire->telephone ?? '', $blockContent);
                $blockContent = str_replace('{{Locataire_Profession}}', $locataire->profession ?? '', $blockContent);

                $result .= $blockContent;
            }

            return $result;
        }, $content);
    }

    /**
     * Traiter la boucle des garants
     */
    private function processGarantLoop(string $content, $garants): string
    {
        $pattern = '/{{GarantBlockStart}}(.*?){{GarantBlockEnd}}/s';

        return preg_replace_callback($pattern, function ($matches) use ($garants) {
            $blockTemplate = $matches[1];
            $result = '';

            foreach ($garants as $garant) {
                $blockContent = $blockTemplate;

                $blockContent = str_replace('{{Garant_Nom}}', $garant->nom ?? '', $blockContent);
                $blockContent = str_replace('{{Garant_Prenom}}', $garant->prenom ?? '', $blockContent);
                $blockContent = str_replace('{{Garant_NomComplet}}', $garant->nom_complet ?? '', $blockContent);
                $blockContent = str_replace('{{Garant_Adresse}}', $garant->adresse ?? '', $blockContent);
                $blockContent = str_replace('{{Garant_Telephone}}', $garant->telephone ?? '', $blockContent);
                $blockContent = str_replace('{{Garant_Email}}', $garant->email ?? '', $blockContent);

                $result .= $blockContent;
            }

            return $result;
        }, $content);
    }

    /**
     * Insérer le logo dans le contenu
     */
    private function insertLogo(string $content, string $logoPath): string
    {
        $logoHtml = '<div style="text-align: center; margin-bottom: 20px;"><img src="' . public_path('storage/' . $logoPath) . '" style="max-height: 100px; max-width: 300px;"></div>';

        // Insérer après la balise <body> si présente
        if (str_contains($content, '<body>')) {
            $content = str_replace('<body>', '<body>' . $logoHtml, $content);
        } else {
            // Sinon, ajouter au début
            $content = $logoHtml . $content;
        }

        return $content;
    }

    /**
     * Insérer la signature dans le contenu
     */
    private function insertSignature(string $content, string $signaturePath): string
    {
        $signatureHtml = '<div style="margin-top: 40px; text-align: right;"><p style="margin-bottom: 10px;"><strong>Signature :</strong></p><img src="' . public_path('storage/' . $signaturePath) . '" style="max-height: 80px; max-width: 200px;"></div>';

        // Insérer avant </body> si présent
        if (str_contains($content, '</body>')) {
            $content = str_replace('</body>', $signatureHtml . '</body>', $content);
        } else {
            // Sinon, ajouter à la fin
            $content .= $signatureHtml;
        }

        return $content;
    }

    /**
     * Insérer le footer dans le contenu
     */
    private function insertFooter(string $content, string $footerText): string
    {
        $footerHtml = '<div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb; font-size: 10pt; color: #6b7280; text-align: center;">' . nl2br(e($footerText)) . '</div>';

        // Insérer avant </body> si présent
        if (str_contains($content, '</body>')) {
            $content = str_replace('</body>', $footerHtml . '</body>', $content);
        } else {
            $content .= $footerHtml;
        }

        return $content;
    }

    /**
     * Générer un PDF
     */
    private function generatePDF(string $content, DocumentTemplate $template, Contrat $contrat): string
    {
        $pdf = Pdf::loadHTML($content);
        $pdf->setPaper('A4', 'portrait');

        // Nom du fichier
        $fileName = $this->generateFileName($template, $contrat, 'pdf');
        $filePath = 'documents/' . $fileName;

        // Sauvegarder avec Storage
        Storage::put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Générer le nom du fichier
     */
    private function generateFileName(DocumentTemplate $template, Contrat $contrat, string $extension): string
    {
        $slug = Str::slug($template->nom);
        $date = now()->format('Y-m-d');
        $reference = Str::slug($contrat->reference);
        $unique = substr(md5(uniqid()), 0, 8);

        return "{$slug}_{$reference}_{$date}_{$unique}.{$extension}";
    }

    /**
     * Générer le nom du document
     */
    private function generateDocumentName(DocumentTemplate $template, Contrat $contrat): string
    {
        return "{$template->nom} - {$contrat->reference} - " . now()->format('d-m-Y');
    }
}