<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->enum('type_bail', ['vide', 'meuble', 'commercial', 'professionnel', 'parking']);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->integer('duree_mois')->default(12);
            $table->decimal('loyer_hc', 10, 2);
            $table->decimal('charges', 10, 2)->default(0);
            $table->decimal('loyer_cc', 10, 2);
            $table->decimal('depot_garantie', 10, 2)->default(0);
            $table->enum('periodicite_paiement', ['mensuel', 'trimestriel', 'annuel'])->default('mensuel');
            $table->integer('jour_paiement')->default(1);
            $table->decimal('indice_reference', 8, 2)->nullable();
            $table->date('date_revision')->nullable();
            $table->boolean('tacite_reconduction')->default(true);
            $table->enum('statut', ['brouillon', 'actif', 'resilie', 'termine', 'suspendu'])->default('brouillon');
            $table->date('date_signature')->nullable();
            $table->text('conditions_particulieres')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};