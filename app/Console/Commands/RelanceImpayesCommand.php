<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Notification;
use App\Mail\RelanceLoyer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RelanceImpayesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:relances {--days=7 : Nombre de jours après génération}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer des relances automatiques pour les quittances impayées après X jours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dateLimit = now()->subDays($days);

        $this->info("🔔 Vérification des quittances impayées (générées il y a {$days} jours ou plus)...");
        $this->info('📅 Date limite : ' . $dateLimit->format('d/m/Y'));

        // Récupérer les quittances générées il y a X jours ou plus
        // On suppose qu'une quittance non payée n'a pas de log "paye"
        $quittancesImpayees = Document::with(['contrat.locataires', 'contrat.bien'])
            ->where('type', 'quittance_loyer')
            ->where('created_at', '<=', $dateLimit)
            ->whereDoesntHave('logs', function($query) {
                $query->where('action', 'paye');
            })
            ->get();

        // Filtrer celles qui n'ont pas déjà reçu de relance ces 7 derniers jours
        $aRelancer = $quittancesImpayees->filter(function($quittance) {
            // Vérifier s'il y a déjà une notification de relance récente (< 7 jours)
            $derniereRelance = Notification::where('document_id', $quittance->id)
                ->where('type', 'relance')
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            return !$derniereRelance;
        });

        if ($aRelancer->isEmpty()) {
            $this->info('✅ Aucune quittance impayée nécessitant une relance.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$aRelancer->count()} quittance(s) impayée(s) à relancer.");

        $sent = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($aRelancer->count());
        $progressBar->start();

        foreach ($aRelancer as $quittance) {
            $progressBar->advance();

            try {
                $contrat = $quittance->contrat;
                $locatairePrincipal = $contrat->locataire_principal;

                if (!$locatairePrincipal || !$locatairePrincipal->email) {
                    $this->warn("\n⚠️  Pas d'email locataire pour contrat {$contrat->reference}");
                    continue;
                }

                // Calculer le montant et les jours de retard
                $joursRetard = now()->diffInDays($quittance->created_at);
                $montant = $contrat->loyer_cc;

                // Créer la notification pour le gestionnaire
                $gestionnaires = \App\Models\User::whereIn('role', ['super_admin', 'gestionnaire'])->get();
                
                foreach ($gestionnaires as $gestionnaire) {
                    Notification::creerRelance(
                        $gestionnaire->id,
                        $contrat->id,
                        $quittance->id,
                        [
                            'titre' => "Loyer impayé - {$contrat->reference}",
                            'message' => "Le loyer du {$quittance->created_at->format('d/m/Y')} n'a pas été réglé ({$joursRetard} jours de retard). Montant : {$montant} €.",
                            'priorite' => $joursRetard > 14 ? 'urgente' : 'haute',
                            'metadata' => [
                                'montant' => $montant,
                                'jours_retard' => $joursRetard,
                                'locataire' => $locatairePrincipal->nom_complet,
                            ],
                        ]
                    );
                }

                // Envoyer l'email de relance au locataire
                Mail::to($locatairePrincipal->email)->queue(
                    new RelanceLoyer($quittance, $contrat, $joursRetard)
                );

                // Logger l'action
                $quittance->logAction('relance', null, "Relance automatique ({$joursRetard} jours)", $locatairePrincipal->email);

                $sent++;

                $this->info("\n📧 Relance envoyée pour {$contrat->reference} ({$joursRetard} jours)");

            } catch (\Exception $e) {
                $this->error("\n❌ Erreur relance {$quittance->id} : " . $e->getMessage());
                Log::error('Erreur relance impayé', [
                    'document_id' => $quittance->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ Relances terminées !');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['📧 Envoyées', $sent],
                ['❌ Erreurs', $errors],
            ]
        );

        Log::info('Relances impayés', [
            'sent' => $sent,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}