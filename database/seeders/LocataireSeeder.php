<?php

namespace Database\Seeders;

use App\Models\Locataire;
use Illuminate\Database\Seeder;

class LocataireSeeder extends Seeder
{
    public function run(): void
    {
        $locataires = [
            [
                'nom' => 'Bernard',
                'prenom' => 'Marie',
                'date_naissance' => '1990-05-15',
                'lieu_naissance' => 'Paris',
                'adresse_actuelle' => '25 Rue de la Paix',
                'code_postal' => '75002',
                'ville' => 'Paris',
                'email' => 'marie.bernard@example.com',
                'telephone' => '0634567890',
                'profession' => 'Ingénieure',
                'employeur' => 'Tech Company SAS',
                'revenus_mensuels' => 3500.00,
            ],
            [
                'nom' => 'Petit',
                'prenom' => 'Thomas',
                'date_naissance' => '1995-08-22',
                'lieu_naissance' => 'Lyon',
                'adresse_actuelle' => '67 Avenue Jean Jaurès',
                'code_postal' => '69007',
                'ville' => 'Lyon',
                'email' => 'thomas.petit@example.com',
                'telephone' => '0645678901',
                'profession' => 'Étudiant',
                'employeur' => 'Université Lyon 1',
                'revenus_mensuels' => 800.00,
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Pierre',
                'date_naissance' => '1985-03-10',
                'lieu_naissance' => 'Marseille',
                'adresse_actuelle' => '12 Boulevard Voltaire',
                'code_postal' => '75011',
                'ville' => 'Paris',
                'email' => 'pierre.dubois@example.com',
                'telephone' => '0656789012',
                'profession' => 'Consultant',
                'employeur' => 'Cabinet Conseil',
                'revenus_mensuels' => 4200.00,
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Julie',
                'date_naissance' => '1992-11-30',
                'lieu_naissance' => 'Nantes',
                'adresse_actuelle' => '89 Rue du Commerce',
                'code_postal' => '75015',
                'ville' => 'Paris',
                'email' => 'julie.moreau@example.com',
                'telephone' => '0667890123',
                'profession' => 'Architecte',
                'employeur' => 'Studio Architecture',
                'revenus_mensuels' => 3800.00,
            ],
            [
                'nom' => 'Laurent',
                'prenom' => 'Nicolas',
                'date_naissance' => '1988-07-18',
                'lieu_naissance' => 'Bordeaux',
                'adresse_actuelle' => '34 Rue Nationale',
                'code_postal' => '75013',
                'ville' => 'Paris',
                'email' => 'nicolas.laurent@example.com',
                'telephone' => '0678901234',
                'profession' => 'Chef de projet',
                'employeur' => 'Digital Agency',
                'revenus_mensuels' => 4500.00,
            ],
        ];

        foreach ($locataires as $locataire) {
            Locataire::create($locataire);
        }
    }
}