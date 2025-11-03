<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'nom' => 'Contrat de bail vide standard',
                'type' => 'bail_vide',
                'actif' => true,
                'is_default' => true,
                'biens_concernes' => null, // ✅ AJOUTÉ : null = tous les biens
                'contenu' => $this->getBailVideTemplate(),
            ],
            [
                'nom' => 'Contrat de bail meublé standard',
                'type' => 'bail_meuble',
                'actif' => true,
                'is_default' => true,
                'biens_concernes' => null, // ✅ AJOUTÉ
                'contenu' => $this->getBailMeubleTemplate(),
            ],
            [
                'nom' => 'Contrat de bail parking',
                'type' => 'bail_parking',
                'actif' => true,
                'is_default' => true,
                'biens_concernes' => ['parking', 'garage'], // ✅ AJOUTÉ : uniquement parking/garage
                'contenu' => $this->getBailParkingTemplate(),
            ],
            [
                'nom' => 'Quittance de loyer standard',
                'type' => 'quittance_loyer',
                'actif' => true,
                'is_default' => true,
                'biens_concernes' => null, // ✅ AJOUTÉ
                'contenu' => $this->getQuittanceTemplate(),
            ],
            [
                'nom' => 'État des lieux d\'entrée',
                'type' => 'etat_lieux_entree',
                'actif' => true,
                'is_default' => true,
                'biens_concernes' => null, // ✅ AJOUTÉ
                'contenu' => $this->getEtatLieuxTemplate(),
            ],
        ];

        foreach ($templates as $template) {
            DocumentTemplate::create($template);
        }

        $this->command->info('✅ 5 modèles de documents créés avec succès !');
    }

    private function getBailVideTemplate(): string
    {
        return <<<HTML
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18pt; font-weight: bold; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">CONTRAT DE LOCATION VIDE</h1>
        <p>Loi n° 89-462 du 6 juillet 1989</p>
    </div>

    <div class="section">
        <p class="section-title">ENTRE LES SOUSSIGNÉS :</p>
        <p>
            <strong>Le Bailleur :</strong><br>
            {{Proprietaire_NomComplet}}<br>
            {{Proprietaire_Adresse}}<br>
            {{Proprietaire_CodePostal}} {{Proprietaire_Ville}}<br>
            Email : {{Proprietaire_Email}}<br>
            Téléphone : {{Proprietaire_Telephone}}
        </p>
    </div>

    <div class="section">
        <p><strong>D'UNE PART,</strong></p>
    </div>

    <div class="section">
        <p class="section-title">ET :</p>
        {{LocataireBlockStart}}
        <p>
            <strong>Le Locataire :</strong><br>
            {{Locataire_NomComplet}}<br>
            Né(e) le {{Locataire_DateNaissance}}<br>
            {{Locataire_Adresse}}<br>
            {{Locataire_CodePostal}} {{Locataire_Ville}}<br>
            Email : {{Locataire_Email}}<br>
            Téléphone : {{Locataire_Telephone}}
        </p>
        {{LocataireBlockEnd}}
    </div>

    <div class="section">
        <p><strong>D'AUTRE PART,</strong></p>
    </div>

    <div class="section">
        <p class="section-title">ARTICLE 1 - OBJET DU CONTRAT</p>
        <p>
            Le bailleur donne en location au locataire qui accepte, le logement suivant :<br>
            <strong>Adresse :</strong> {{Bien_Adresse}}, {{Bien_CodePostal}} {{Bien_Ville}}<br>
            <strong>Type :</strong> {{Bien_Type}}<br>
            <strong>Surface :</strong> {{Bien_Surface}} m²<br>
            <strong>Nombre de pièces :</strong> {{Bien_NombrePieces}}<br>
            <strong>Étage :</strong> {{Bien_Etage}}<br>
            <strong>DPE :</strong> {{Bien_DPE}}
        </p>
    </div>

    <div class="section">
        <p class="section-title">ARTICLE 2 - DURÉE DU BAIL</p>
        <p>
            Le présent bail est consenti et accepté pour une durée de {{Contrat_DureeMois}} mois.<br>
            <strong>Date de début :</strong> {{Contrat_DateDebut}}<br>
            <strong>Date de fin :</strong> {{Contrat_DateFin}}
        </p>
    </div>

    <div class="section">
        <p class="section-title">ARTICLE 3 - LOYER ET CHARGES</p>
        <p>
            <strong>Loyer mensuel hors charges :</strong> {{Contrat_LoyerHC}} €<br>
            <strong>Provision pour charges :</strong> {{Contrat_Charges}} €<br>
            <strong>Loyer charges comprises :</strong> {{Contrat_LoyerCC}} €<br>
            <br>
            Le loyer est payable le 1er de chaque mois.
        </p>
    </div>

    <div class="section">
        <p class="section-title">ARTICLE 4 - DÉPÔT DE GARANTIE</p>
        <p>
            Le locataire verse au bailleur un dépôt de garantie d'un montant de {{Contrat_DepotGarantie}} €.
        </p>
    </div>

    <div class="section">
        <p class="section-title">SIGNATURES</p>
        <p>
            Fait à {{Bien_Ville}}, le {{Date_Aujourd_hui}}<br>
            En deux exemplaires originaux
        </p>
        <br>
        <table width="100%">
            <tr>
                <td width="50%">
                    <p><strong>Le Bailleur</strong></p>
                    <p>{{Proprietaire_NomComplet}}</p>
                </td>
                <td width="50%">
                    <p><strong>Le Locataire</strong></p>
                    <p>{{Locataire_NomComplet}}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;
    }

    private function getBailMeubleTemplate(): string
    {
        return <<<HTML
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">CONTRAT DE LOCATION MEUBLÉE</h1>
        <p>Loi n° 89-462 du 6 juillet 1989</p>
    </div>
    <p><strong>Bailleur :</strong> {{Proprietaire_NomComplet}}</p>
    <p><strong>Locataire :</strong> {{Locataire_NomComplet}}</p>
    <p><strong>Bien :</strong> {{Bien_Adresse}}, {{Bien_Ville}}</p>
    <p><strong>Loyer charges comprises :</strong> {{Contrat_LoyerCC}} €</p>
</body>
</html>
HTML;
    }

    private function getBailParkingTemplate(): string
    {
        return <<<HTML
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">CONTRAT DE LOCATION DE PARKING</h1>
    </div>

    <div class="section">
        <p><strong>Bailleur :</strong><br>
        {{Proprietaire_NomComplet}}<br>
        {{Proprietaire_Adresse}}<br>
        {{Proprietaire_CodePostal}} {{Proprietaire_Ville}}</p>
    </div>

    <div class="section">
        <p><strong>Locataire :</strong><br>
        {{Locataire_NomComplet}}<br>
        {{Locataire_Adresse}}<br>
        {{Locataire_CodePostal}} {{Locataire_Ville}}</p>
    </div>

    <div class="section">
        <p><strong>Emplacement :</strong><br>
        {{Bien_Adresse}}, {{Bien_CodePostal}} {{Bien_Ville}}<br>
        Type : {{Bien_Type}}</p>
    </div>

    <div class="section">
        <p><strong>Loyer mensuel :</strong> {{Contrat_LoyerCC}} €</p>
        <p><strong>Durée :</strong> Du {{Contrat_DateDebut}} au {{Contrat_DateFin}}</p>
    </div>

    <p>Fait à {{Bien_Ville}}, le {{Date_Aujourd_hui}}</p>
</body>
</html>
HTML;
    }

    private function getQuittanceTemplate(): string
    {
        return <<<HTML
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 16pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td { padding: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">QUITTANCE DE LOYER</h1>
    </div>

    <p>
        <strong>Bailleur :</strong><br>
        {{Proprietaire_NomComplet}}<br>
        {{Proprietaire_Adresse}}<br>
        {{Proprietaire_CodePostal}} {{Proprietaire_Ville}}
    </p>

    <p>
        <strong>Locataire :</strong><br>
        {{Locataire_NomComplet}}<br>
        {{Bien_Adresse}}<br>
        {{Bien_CodePostal}} {{Bien_Ville}}
    </p>

    <table border="1">
        <tr>
            <td><strong>Loyer hors charges</strong></td>
            <td align="right">{{Contrat_LoyerHC}} €</td>
        </tr>
        <tr>
            <td><strong>Charges</strong></td>
            <td align="right">{{Contrat_Charges}} €</td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td align="right"><strong>{{Contrat_LoyerCC}} €</strong></td>
        </tr>
    </table>

    <p>
        Je soussigné(e) {{Proprietaire_NomComplet}}, propriétaire du logement situé {{Bien_Adresse}},
        reconnais avoir reçu de {{Locataire_NomComplet}} la somme de {{Contrat_LoyerCC}} euros
        au titre du loyer et des charges.
    </p>

    <p>
        Fait à {{Bien_Ville}}, le {{Date_Aujourd_hui}}
    </p>

    <p><strong>Signature du bailleur</strong></p>
</body>
</html>
HTML;
    }

    private function getEtatLieuxTemplate(): string
    {
        return <<<HTML
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">ÉTAT DES LIEUX D'ENTRÉE</h1>
    </div>
    <p><strong>Bien :</strong> {{Bien_Adresse}}, {{Bien_Ville}}</p>
    <p><strong>Locataire :</strong> {{Locataire_NomComplet}}</p>
    <p><strong>Date :</strong> {{Date_Aujourd_hui}}</p>
    
    <h3>DESCRIPTION DES PIÈCES</h3>
    <p><em>(À compléter lors de l'état des lieux)</em></p>
</body>
</html>
HTML;
    }
}