<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etats_des_lieux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained('contrats')->onDelete('set null');
            $table->enum('type', ['entree', 'sortie'])->default('entree');
            $table->enum('statut', ['brouillon', 'termine'])->default('brouillon');
            $table->date('date_etat');
            $table->text('observations_generales')->nullable();
            
            // Relevés de compteurs (JSON)
            $table->json('compteurs_eau')->nullable();
            $table->json('compteurs_gaz')->nullable();
            $table->json('compteurs_electricite')->nullable();
            $table->json('chauffage')->nullable();
            $table->json('eau_chaude')->nullable();
            
            // Remise des clés (JSON)
            $table->json('cles')->nullable();
            
            // Autres aménagements (JSON)
            $table->json('autres_amenagements')->nullable();
            
            // Document généré
            $table->foreignId('document_id')->nullable()->constrained('documents')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etats_des_lieux');
    }
};