<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('adresse');
            $table->string('code_postal', 10);
            $table->string('ville');
            $table->string('pays')->default('France');
            $table->enum('type', ['appartement', 'maison', 'studio', 'parking', 'garage', 'local_commercial', 'bureau', 'terrain']);
            $table->decimal('surface', 8, 2)->nullable();
            $table->integer('nombre_pieces')->nullable();
            $table->integer('etage')->nullable();
            $table->string('dpe')->nullable();
            $table->enum('statut', ['disponible', 'loue', 'maintenance', 'vendu'])->default('disponible');
            $table->text('description')->nullable();
             $table->json('photos')->nullable();
            $table->decimal('rentabilite', 5, 2)->nullable();
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};