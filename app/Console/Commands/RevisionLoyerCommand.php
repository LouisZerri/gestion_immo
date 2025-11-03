<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RevisionLoyerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:revisions {--days=30 : Nombre de jours avant/après la date de révision}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer des notifications pour les révisions de loyer dues ou à venir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dateLimitePasse = now()->subDays($days);
        $dateLimiteFutur = now()->addDays($days);

        $this->info("🔔 Vérification des révisions de loyer ({$days} jours avant/après)...");
        $this->info('📅 Période : ' . $dateLimitePasse->format('d/m/Y') . ' → ' . $dateLimiteFutur->format('d/m/Y'));

        // Récupérer les contrats actifs avec date_revision dans la période
        $contratsAReviser = Contrat::with(['bien', 'locataires'])
            ->where('statut', 'actif')
            ->whereNotNull('date_revision')
            ->whereBetween('date_revision', [$dateLimitePasse, $dateLimiteFutur])
            ->get();

        // Filtrer ceux qui n'ont pas déjà une notification de révision récente (< 60 jours)
        $contratsANotifier = $contratsAReviser->filter(function($contrat) {
            $notificationRecente = Notification::where('contrat_id', $contrat->id)
                ->where('type', 'revision')
                ->where('created_at', '>=', now()->subDays(60))
                ->exists();

            return !$notificationRecente;
        });

        if ($contratsANotifier->isEmpty()) {
            $this->info('✅ Aucune révision de loyer à notifier.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$contratsANotifier->count()} révision(s) de loyer à notifier.");

        $created = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($contratsANotifier->count());
        $progressBar->start();

        foreach ($contratsANotifier as $contrat) {
            $progressBar->advance();

            try {
                $dateRevision = $contrat->date_revision;
                $estPassee = $dateRevision < now();
                $joursEcart = abs(now()->diffInDays($dateRevision));

                // Message selon si passée ou à venir
                if ($estPassee) {
                    $message = "La date de révision annuelle du loyer était le {$dateRevision->format('d/m/Y')} (il y a {$joursEcart} jours). Pensez à calculer et appliquer la révision basée sur l'IRL.";
                    $priorite = $joursEcart > 30 ? 'haute' : 'normale';
                } else {
                    $message = "La date de révision annuelle du loyer approche : {$dateRevision->format('d/m/Y')} (dans {$joursEcart} jours). Préparez le calcul avec l'IRL.";
                    $priorite = 'normale';
                }

                // Calculer une suggestion de nouveau loyer (si IRL disponible)
                $nouveauLoyer = null;
                if ($contrat->indice_reference) {
                    // Exemple simple : +2% (à adapter avec le vrai IRL)
                    $nouveauLoyer = round($contrat->loyer_hc * 1.02, 2);
                }

                // Récupérer tous les gestionnaires
                $gestionnaires = \App\Models\User::whereIn('role', ['super_admin', 'gestionnaire'])->get();

                foreach ($gestionnaires as $gestionnaire) {
                    Notification::creerRevision(
                        $gestionnaire->id,
                        $contrat->id,
                        [
                            'titre' => "Révision de loyer - {$contrat->reference}",
                            'message' => $message,
                            'priorite' => $priorite,
                            'metadata' => [
                                'date_revision' => $dateRevision->format('Y-m-d'),
                                'jours_ecart' => $joursEcart,
                                'est_passee' => $estPassee,
                                'loyer_actuel' => $contrat->loyer_hc,
                                'nouveau_loyer_suggere' => $nouveauLoyer,
                                'indice_reference' => $contrat->indice_reference,
                                'adresse_bien' => $contrat->bien->adresse,
                            ],
                        ]
                    );
                }

                $created++;

                $this->info("\n🔔 Notification créée pour {$contrat->reference} (" . 
                    ($estPassee ? "{$joursEcart}j passés" : "dans {$joursEcart}j") . ")");

            } catch (\Exception $e) {
                $this->error("\n❌ Erreur contrat {$contrat->reference} : " . $e->getMessage());
                Log::error('Erreur notification révision', [
                    'contrat_id' => $contrat->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ Notifications de révision créées !');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['🔔 Créées', $created],
                ['❌ Erreurs', $errors],
            ]
        );

        Log::info('Notifications révision loyers', [
            'created' => $created,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}