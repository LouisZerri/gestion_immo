<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['particulier', 'societe'])->default('particulier');
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('nom_societe')->nullable();
            $table->string('siret')->nullable();
            $table->string('adresse');
            $table->string('code_postal', 10);
            $table->string('ville');
            $table->string('pays')->default('France');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('telephone_secondaire')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->boolean('mandat_actif')->default(false);
            $table->date('date_debut_mandat')->nullable();
            $table->date('date_fin_mandat')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proprietaires');
    }
};