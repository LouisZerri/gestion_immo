<?php

namespace App\Console\Commands;

use App\Models\QuittanceAutomatisee;
use App\Models\Notification;
use App\Services\DocumentGeneratorService;
use App\Mail\QuittanceLoyer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuittanceGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quittances:generate {--force : Forcer la génération même si déjà fait}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer automatiquement les quittances de loyer pour tous les contrats actifs';

    protected $generatorService;

    public function __construct(DocumentGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Démarrage de la génération automatique des quittances...');
        $this->info('📅 Date : ' . now()->format('d/m/Y H:i'));

        // Récupérer toutes les configurations actives dues aujourd'hui
        $quittancesAGenerer = QuittanceAutomatisee::with(['contrat.bien', 'contrat.locataires', 'template'])
            ->actives()
            ->duesAujourdhui()
            ->get();

        if ($quittancesAGenerer->isEmpty()) {
            $this->info('✅ Aucune quittance à générer aujourd\'hui.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$quittancesAGenerer->count()} quittance(s) à générer.");

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($quittancesAGenerer->count());
        $progressBar->start();

        foreach ($quittancesAGenerer as $config) {
            $progressBar->advance();

            try {
                // Vérifier que le contrat est toujours actif
                if ($config->contrat->statut !== 'actif') {
                    $this->warn("\n⚠️  Contrat {$config->contrat->reference} non actif - ignoré");
                    $skipped++;
                    continue;
                }

                // Vérifier qu'on n'a pas déjà généré ce mois-ci (sauf --force)
                if (!$this->option('force') && $config->derniere_generation && $config->derniere_generation->isSameMonth(now())) {
                    $this->warn("\n⏭️  Quittance déjà générée ce mois pour {$config->contrat->reference} - ignoré");
                    $skipped++;
                    continue;
                }

                // Récupérer le template (ou utiliser le template par défaut)
                $template = $config->template ?? \App\Models\DocumentTemplate::where('type', $config->type)
                    ->where('actif', true)
                    ->where('modele_defaut', true)
                    ->first();

                if (!$template) {
                    $this->error("\n❌ Aucun modèle trouvé pour {$config->contrat->reference}");
                    $errors++;
                    continue;
                }

                // Générer le document
                $document = $this->generatorService->generate(
                    $template,
                    $config->contrat,
                    'pdf',
                    null // Généré automatiquement, pas d'utilisateur
                );

                // Marquer comme générée
                $config->marquerGeneree();

                $generated++;

                // Envoi automatique par email si activé
                if ($config->envoi_automatique) {
                    $this->envoyerParEmail($config, $document);
                }

                // Créer une notification pour le gestionnaire
                $this->creerNotificationGestionnaire($config, $document);

            } catch (\Exception $e) {
                $this->error("\n❌ Erreur pour {$config->contrat->reference} : " . $e->getMessage());
                Log::error('Erreur génération quittance', [
                    'config_id' => $config->id,
                    'contrat_id' => $config->contrat_id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ Génération terminée !');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['✅ Générées', $generated],
                ['⏭️  Ignorées', $skipped],
                ['❌ Erreurs', $errors],
            ]
        );

        Log::info('Génération automatique quittances', [
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Envoyer la quittance par email
     */
    private function envoyerParEmail(QuittanceAutomatisee $config, $document): void
    {
        try {
            // Email destinataire : soit défini dans config, soit email du locataire principal
            $destinataire = $config->email_destinataire ?? $config->contrat->locataire_principal?->email;

            if (!$destinataire) {
                $this->warn("\n⚠️  Pas d'email destinataire pour {$config->contrat->reference}");
                return;
            }

            // Envoyer l'email en queue
            Mail::to($destinataire)->queue(new QuittanceLoyer($document, $config->contrat));

            // Logger l'envoi
            $document->logAction('envoye', null, 'Envoi automatique par email', $destinataire);

            $this->info("\n📧 Email envoyé à {$destinataire}");

        } catch (\Exception $e) {
            $this->error("\n❌ Erreur envoi email : " . $e->getMessage());
            Log::error('Erreur envoi email quittance', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Créer une notification pour le gestionnaire
     */
    private function creerNotificationGestionnaire(QuittanceAutomatisee $config, $document): void
    {
        // Récupérer tous les gestionnaires et super admins
        $gestionnaires = \App\Models\User::whereIn('role', ['super_admin', 'gestionnaire'])->get();

        foreach ($gestionnaires as $gestionnaire) {
            Notification::create([
                'type' => 'generale',
                'user_id' => $gestionnaire->id,
                'contrat_id' => $config->contrat_id,
                'document_id' => $document->id,
                'titre' => 'Quittance générée automatiquement',
                'message' => "Une quittance a été générée pour le contrat {$config->contrat->reference} ({$config->contrat->bien->adresse}).",
                'priorite' => 'basse',
                'metadata' => [
                    'type_document' => $config->type,
                    'montant' => $config->contrat->loyer_cc,
                ],
            ]);
        }
    }
}