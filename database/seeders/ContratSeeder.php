<?php

namespace Database\Seeders;

use App\Models\Contrat;
use Illuminate\Database\Seeder;

class ContratSeeder extends Seeder
{
    public function run(): void
    {
        $contrats = [
            [
                'reference' => 'BAIL-2024-001',
                'bien_id' => 1,
                'proprietaire_id' => 1,
                'type_bail' => 'vide',
                'date_debut' => now()->subMonths(6),
                'date_fin' => now()->addMonths(6),
                'duree_mois' => 12,
                'loyer_hc' => 1200.00,
                'charges' => 150.00,
                'loyer_cc' => 1350.00,
                'depot_garantie' => 1200.00,
                'periodicite_paiement' => 'mensuel',
                'jour_paiement' => 1,
                'indice_reference' => 130.52,
                'date_revision' => now()->addMonths(6),
                'tacite_reconduction' => true,
                'statut' => 'actif',
                'date_signature' => now()->subMonths(6),
            ],
            [
                'reference' => 'BAIL-2024-002',
                'bien_id' => 3,
                'proprietaire_id' => 3,
                'type_bail' => 'meuble',
                'date_debut' => now()->subMonths(3),
                'date_fin' => now()->addMonths(9),
                'duree_mois' => 12,
                'loyer_hc' => 2800.00,
                'charges' => 300.00,
                'loyer_cc' => 3100.00,
                'depot_garantie' => 5600.00,
                'periodicite_paiement' => 'mensuel',
                'jour_paiement' => 5,
                'indice_reference' => 130.52,
                'date_revision' => now()->addMonths(9),
                'tacite_reconduction' => true,
                'statut' => 'actif',
                'date_signature' => now()->subMonths(3),
            ],
            [
                'reference' => 'BAIL-2024-003',
                'bien_id' => 4,
                'proprietaire_id' => 1,
                'type_bail' => 'parking',
                'date_debut' => now()->subYear(),
                'date_fin' => now()->addMonths(12),
                'duree_mois' => 24,
                'loyer_hc' => 100.00,
                'charges' => 0.00,
                'loyer_cc' => 100.00,
                'depot_garantie' => 0.00,
                'periodicite_paiement' => 'mensuel',
                'jour_paiement' => 1,
                'tacite_reconduction' => true,
                'statut' => 'actif',
                'date_signature' => now()->subYear(),
            ],
        ];

        foreach ($contrats as $contratData) {
            $contrat = Contrat::create($contratData);
            
            // Attacher les locataires aux contrats
            if ($contrat->id === 1) {
                $contrat->locataires()->attach(1, ['titulaire_principal' => true]);
            } elseif ($contrat->id === 2) {
                // Colocation
                $contrat->locataires()->attach(3, ['titulaire_principal' => true, 'part_loyer' => 1550.00]);
                $contrat->locataires()->attach(4, ['titulaire_principal' => false, 'part_loyer' => 1550.00]);
            } elseif ($contrat->id === 3) {
                $contrat->locataires()->attach(5, ['titulaire_principal' => true]);
            }
        }
    }
}