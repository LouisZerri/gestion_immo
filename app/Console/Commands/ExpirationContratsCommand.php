<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirationContratsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:expirations {--months=3 : Nombre de mois avant expiration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer des notifications pour les contrats arrivant à échéance dans X mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int) $this->option('months');
        $dateLimit = now()->addMonths($months);

        $this->info("🔔 Vérification des contrats expirant dans les {$months} prochains mois...");
        $this->info('📅 Date limite : ' . $dateLimit->format('d/m/Y'));

        // Récupérer les contrats actifs avec date_fin dans les X prochains mois
        $contratsExpirants = Contrat::with(['bien', 'locataires'])
            ->where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), $dateLimit])
            ->get();

        // Filtrer ceux qui n'ont pas déjà une notification d'expiration récente (< 30 jours)
        $contratsANotifier = $contratsExpirants->filter(function($contrat) {
            $notificationRecente = Notification::where('contrat_id', $contrat->id)
                ->where('type', 'expiration')
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            return !$notificationRecente;
        });

        if ($contratsANotifier->isEmpty()) {
            $this->info('✅ Aucun contrat expirant nécessitant une notification.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$contratsANotifier->count()} contrat(s) expirant bientôt.");

        $created = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($contratsANotifier->count());
        $progressBar->start();

        foreach ($contratsANotifier as $contrat) {
            $progressBar->advance();

            try {
                $joursRestants = now()->diffInDays($contrat->date_fin);
                $moisRestants = round($joursRestants / 30, 1);

                // Priorité selon le temps restant
                $priorite = match(true) {
                    $joursRestants <= 30 => 'urgente',
                    $joursRestants <= 60 => 'haute',
                    default => 'normale',
                };

                // Récupérer tous les gestionnaires
                $gestionnaires = \App\Models\User::whereIn('role', ['super_admin', 'gestionnaire'])->get();

                foreach ($gestionnaires as $gestionnaire) {
                    Notification::creerExpiration(
                        $gestionnaire->id,
                        $contrat->id,
                        [
                            'titre' => "Contrat expirant - {$contrat->reference}",
                            'message' => "Le contrat pour {$contrat->bien->adresse} expire le {$contrat->date_fin->format('d/m/Y')} ({$moisRestants} mois restants). Pensez à le renouveler ou à prévoir la fin de location.",
                            'priorite' => $priorite,
                            'metadata' => [
                                'date_fin' => $contrat->date_fin->format('Y-m-d'),
                                'jours_restants' => $joursRestants,
                                'mois_restants' => $moisRestants,
                                'adresse_bien' => $contrat->bien->adresse,
                                'locataire' => $contrat->locataire_principal?->nom_complet,
                            ],
                        ]
                    );
                }

                $created++;

                $this->info("\n🔔 Notification créée pour {$contrat->reference} ({$moisRestants} mois)");

            } catch (\Exception $e) {
                $this->error("\n❌ Erreur contrat {$contrat->reference} : " . $e->getMessage());
                Log::error('Erreur notification expiration', [
                    'contrat_id' => $contrat->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ Notifications d\'expiration créées !');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['🔔 Créées', $created],
                ['❌ Erreurs', $errors],
            ]
        );

        Log::info('Notifications expiration contrats', [
            'created' => $created,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}