<?php

namespace Database\Seeders;

use App\Models\Bien;
use Illuminate\Database\Seeder;

class BienSeeder extends Seeder
{
    public function run(): void
    {
        $biens = [
            [
                'reference' => 'BIEN-001',
                'adresse' => '10 Rue Victor Hugo',
                'code_postal' => '75016',
                'ville' => 'Paris',
                'type' => 'appartement',
                'surface' => 65.50,
                'nombre_pieces' => 3,
                'etage' => 2,
                'dpe' => 'C',
                'statut' => 'loue',
                'description' => 'Bel appartement T3 avec balcon',
                'rentabilite' => 4.5,
                'proprietaire_id' => 1,
            ],
            [
                'reference' => 'BIEN-002',
                'adresse' => '23 Avenue de la Liberté',
                'code_postal' => '69003',
                'ville' => 'Lyon',
                'type' => 'studio',
                'surface' => 28.00,
                'nombre_pieces' => 1,
                'etage' => 5,
                'dpe' => 'D',
                'statut' => 'disponible',
                'description' => 'Studio refait à neuf, idéal étudiant',
                'rentabilite' => 5.2,
                'proprietaire_id' => 2,
            ],
            [
                'reference' => 'BIEN-003',
                'adresse' => '156 Rue de Rivoli',
                'code_postal' => '75001',
                'ville' => 'Paris',
                'type' => 'appartement',
                'surface' => 120.00,
                'nombre_pieces' => 5,
                'etage' => 3,
                'dpe' => 'B',
                'statut' => 'loue',
                'description' => 'Grand appartement familial, quartier prestigieux',
                'rentabilite' => 3.8,
                'proprietaire_id' => 3,
            ],
            [
                'reference' => 'BIEN-004',
                'adresse' => '8 Impasse du Parc',
                'code_postal' => '75020',
                'ville' => 'Paris',
                'type' => 'parking',
                'surface' => 15.00,
                'dpe' => null,
                'statut' => 'loue',
                'description' => 'Place de parking couverte',
                'rentabilite' => 6.0,
                'proprietaire_id' => 1,
            ],
            [
                'reference' => 'BIEN-005',
                'adresse' => '45 Cours Lafayette',
                'code_postal' => '69006',
                'ville' => 'Lyon',
                'type' => 'local_commercial',
                'surface' => 85.00,
                'dpe' => 'E',
                'statut' => 'disponible',
                'description' => 'Local commercial avec vitrine',
                'proprietaire_id' => 2,
            ],
        ];

        foreach ($biens as $bien) {
            Bien::create($bien);
        }
    }
}