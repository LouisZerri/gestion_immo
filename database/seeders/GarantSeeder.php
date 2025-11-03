<?php

namespace Database\Seeders;

use App\Models\Garant;
use Illuminate\Database\Seeder;

class GarantSeeder extends Seeder
{
    public function run(): void
    {
        $garants = [
            [
                'locataire_id' => 2, // Thomas Petit (étudiant)
                'nom' => 'Petit',
                'prenom' => 'François',
                'date_naissance' => '1965-04-12',
                'adresse' => '15 Rue des Fleurs',
                'code_postal' => '69001',
                'ville' => 'Lyon',
                'email' => 'francois.petit@example.com',
                'telephone' => '0478901234',
                'profession' => 'Directeur commercial',
                'revenus_mensuels' => 5500.00,
                'lien_avec_locataire' => 'parent',
            ],
            [
                'locataire_id' => 4, // Julie Moreau
                'nom' => 'Moreau',
                'prenom' => 'Sylvie',
                'date_naissance' => '1968-09-25',
                'adresse' => '78 Avenue de la Paix',
                'code_postal' => '44000',
                'ville' => 'Nantes',
                'email' => 'sylvie.moreau@example.com',
                'telephone' => '0689012345',
                'profession' => 'Médecin',
                'revenus_mensuels' => 7000.00,
                'lien_avec_locataire' => 'parent',
            ],
        ];

        foreach ($garants as $garant) {
            Garant::create($garant);
        }
    }
}