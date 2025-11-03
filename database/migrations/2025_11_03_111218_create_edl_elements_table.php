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
        Schema::create('edl_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edl_piece_id')->constrained('edl_pieces')->onDelete('cascade');
            $table->string('element'); // MURS A, SOL, PLAFOND, PORTE, etc.
            $table->string('nature')->nullable();
            $table->string('etat_usure')->nullable();
            $table->string('fonctionnement')->nullable();
            $table->text('commentaires')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edl_elements');
    }
};