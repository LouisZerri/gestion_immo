<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
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
                'autre'
            ]);
            $table->longText('contenu');
            $table->boolean('actif')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('biens_concernes')->nullable(); // Pour filtrer par type de bien
            $table->string('logo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->text('footer_text')->nullable();
            $table->json('settings')->nullable(); // Pour stocker config police, styles, etc.
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};