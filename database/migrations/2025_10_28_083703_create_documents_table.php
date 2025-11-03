<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', [
                'bail_vide',
                'bail_meuble',
                'bail_commercial',
                'bail_parking',
                'etat_lieux_entree',
                'etat_lieux_sortie',
                'quittance_loyer',
                'avis_echeance',
                'mandat_gestion',
                'inventaire',
                'attestation_loyer',
                'document_externe',
                'autre'
            ]);
            $table->enum('format', ['pdf', 'docx'])->default('pdf');
            $table->boolean('is_uploaded')->default(false)->comment('true si document uploadé externe, false si généré');
             $table->string('original_filename')->nullable()->comment('Nom original du fichier uploadé');
            $table->string('file_path');
            $table->string('file_type'); // pdf, docx, xlsx, jpg, png
            $table->integer('file_size')->nullable();
            $table->foreignId('bien_id')->nullable()->constrained('biens')->onDelete('set null');
            $table->foreignId('contrat_id')->nullable()->constrained('contrats')->onDelete('set null');
            $table->foreignId('locataire_id')->nullable()->constrained('locataires')->onDelete('set null');
            $table->foreignId('proprietaire_id')->nullable()->constrained('proprietaires')->onDelete('set null');
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->enum('statut', ['brouillon', 'genere', 'envoye', 'signe', 'archive'])->default('genere');
            $table->boolean('is_shared')->default(false);
            $table->json('shared_with')->nullable(); // Liste des IDs et types partagés
            $table->string('share_permissions')->default('view')->comment('Permissions de partage: view, download');
            $table->date('date_envoi')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable()->comment('Tableau des chemins des photos (pour EDL)');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'created_at']);
            $table->index(['bien_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};