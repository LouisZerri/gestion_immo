<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quittances_automatisees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->boolean('actif')->default(true);
            $table->enum('type', ['quittance', 'avis_echeance'])->default('quittance');
            $table->enum('periodicite', ['mensuelle', 'trimestrielle', 'annuelle'])->default('mensuelle');
            $table->integer('jour_generation')->default(1);
            $table->boolean('envoi_automatique')->default(false);
            $table->string('email_destinataire')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->date('derniere_generation')->nullable();
            $table->date('prochaine_generation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quittances_automatisees');
    }
};