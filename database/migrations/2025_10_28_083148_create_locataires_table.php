<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('lieu_naissance')->nullable();
            $table->string('adresse_actuelle');
            $table->string('code_postal', 10);
            $table->string('ville');
            $table->string('pays')->default('France');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('telephone_secondaire')->nullable();
            $table->string('profession')->nullable();
            $table->string('employeur')->nullable();
            $table->decimal('revenus_mensuels', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locataires');
    }
};