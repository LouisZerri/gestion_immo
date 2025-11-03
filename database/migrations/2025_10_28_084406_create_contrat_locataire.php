<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrat_locataire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('locataire_id')->constrained('locataires')->onDelete('cascade');
            $table->boolean('titulaire_principal')->default(false);
            $table->decimal('part_loyer', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['contrat_id', 'locataire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrat_locataire');
    }
};