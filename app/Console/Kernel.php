<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\QuittanceGeneratorCommand::class,
        Commands\RelanceImpayesCommand::class,
        Commands\ExpirationContratsCommand::class,
        Commands\RevisionLoyerCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ========================================
        // GÉNÉRATION AUTOMATIQUE DES QUITTANCES
        // ========================================
        // Tous les 1er du mois à 6h00
        $schedule->command('quittances:generate')
                 ->monthlyOn(1, '06:00')
                 ->timezone('Europe/Paris')
                 ->appendOutputTo(storage_path('logs/quittances-generation.log'));

        // ========================================
        // RELANCES IMPAYÉS
        // ========================================
        // Tous les jours à 9h00 (vérifier les quittances de plus de 7 jours)
        $schedule->command('notifications:relances --days=7')
                 ->dailyAt('09:00')
                 ->timezone('Europe/Paris')
                 ->appendOutputTo(storage_path('logs/relances.log'));

        // ========================================
        // NOTIFICATIONS EXPIRATION CONTRATS
        // ========================================
        // Tous les lundis à 10h00 (contrats expirant dans les 3 prochains mois)
        $schedule->command('notifications:expirations --months=3')
                 ->weeklyOn(1, '10:00') // 1 = Lundi
                 ->timezone('Europe/Paris')
                 ->appendOutputTo(storage_path('logs/expirations.log'));

        // ========================================
        // NOTIFICATIONS RÉVISION LOYER
        // ========================================
        // Le 1er de chaque mois à 8h00 (révisions dans les 30 prochains jours)
        $schedule->command('notifications:revisions --days=30')
                 ->monthlyOn(1, '08:00')
                 ->timezone('Europe/Paris')
                 ->appendOutputTo(storage_path('logs/revisions.log'));

        // ========================================
        // NETTOYAGE (OPTIONNEL)
        // ========================================
        // Supprimer les notifications lues de plus de 90 jours (tous les dimanches à 2h)
        $schedule->call(function () {
            \App\Models\Notification::where('lue', true)
                ->where('lue_le', '<', now()->subDays(90))
                ->delete();
        })->weeklyOn(0, '02:00'); // 0 = Dimanche
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}