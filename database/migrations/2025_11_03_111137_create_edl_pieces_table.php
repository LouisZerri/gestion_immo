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
        Schema::create('edl_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etat_des_lieux_id')->constrained('etats_des_lieux')->onDelete('cascade');
            $table->string('nom_piece'); // ENTREE, SEJOUR, CUISINE, CHAMBRE 1, etc.
            $table->integer('ordre')->default(0);
            $table->text('commentaires_piece')->nullable();
            $table->json('photos')->nullable(); // Array de paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edl_pieces');
    }
};