<?php

namespace Database\Seeders;

use App\Models\Proprietaire;
use Illuminate\Database\Seeder;

class ProprietaireSeeder extends Seeder
{
    public function run(): void
    {
        $proprietaires = [
            [
                'type' => 'particulier',
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'adresse' => '123 Avenue des Champs',
                'code_postal' => '75008',
                'ville' => 'Paris',
                'pays' => 'France',
                'email' => 'jean.dupont@example.com',
                'telephone' => '0612345678',
                'iban' => 'FR7630001007941234567890185',
                'bic' => 'BNPAFRPP',
                'mandat_actif' => true,
                'date_debut_mandat' => now()->subYears(2),
            ],
            [
                'type' => 'particulier',
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'adresse' => '45 Rue de la République',
                'code_postal' => '69002',
                'ville' => 'Lyon',
                'pays' => 'France',
                'email' => 'sophie.martin@example.com',
                'telephone' => '0623456789',
                'iban' => 'FR7612345678901234567890123',
                'mandat_actif' => true,
                'date_debut_mandat' => now()->subYear(),
            ],
            [
                'type' => 'societe',
                'nom' => 'IMMO',
                'nom_societe' => 'IMMO INVEST SAS',
                'siret' => '12345678901234',
                'adresse' => '78 Boulevard Haussmann',
                'code_postal' => '75009',
                'ville' => 'Paris',
                'pays' => 'France',
                'email' => 'contact@immoinvest.com',
                'telephone' => '0145678901',
                'iban' => 'FR7698765432109876543210987',
                'mandat_actif' => true,
                'date_debut_mandat' => now()->subYears(3),
            ],
        ];

        foreach ($proprietaires as $proprietaire) {
            Proprietaire::create($proprietaire);
        }
    }
}