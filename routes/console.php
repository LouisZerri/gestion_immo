<?php

use Illuminate\Support\Facades\Schedule;

// ========================================
// GÉNÉRATION AUTOMATIQUE DES QUITTANCES
// ========================================
// Tous les 1er du mois à 6h00
Schedule::command('quittances:generate')
    ->monthlyOn(1, '06:00')
    ->timezone('Europe/Paris')
    ->appendOutputTo(storage_path('logs/quittances-generation.log'));

// ========================================
// RELANCES IMPAYÉS
// ========================================
// Tous les jours à 9h00 (vérifier les quittances de plus de 7 jours)
Schedule::command('notifications:relances --days=7')
    ->dailyAt('09:00')
    ->timezone('Europe/Paris')
    ->appendOutputTo(storage_path('logs/relances.log'));

// ========================================
// NOTIFICATIONS EXPIRATION CONTRATS
// ========================================
// Tous les lundis à 10h00 (contrats expirant dans les 3 prochains mois)
Schedule::command('notifications:expirations --months=3')
    ->weeklyOn(1, '10:00') // 1 = Lundi
    ->timezone('Europe/Paris')
    ->appendOutputTo(storage_path('logs/expirations.log'));

// ========================================
// NOTIFICATIONS RÉVISION LOYER
// ========================================
// Le 1er de chaque mois à 8h00 (révisions dans les 30 prochains jours)
Schedule::command('notifications:revisions --days=30')
    ->monthlyOn(1, '08:00')
    ->timezone('Europe/Paris')
    ->appendOutputTo(storage_path('logs/revisions.log'));

// ========================================
// NETTOYAGE (OPTIONNEL)
// ========================================
// Supprimer les notifications lues de plus de 90 jours (tous les dimanches à 2h)
Schedule::call(function () {
    \App\Models\Notification::where('lue', true)
        ->where('lue_le', '<', now()->subDays(90))
        ->delete();
})->weeklyOn(0, '02:00'); // 0 = Dimanche