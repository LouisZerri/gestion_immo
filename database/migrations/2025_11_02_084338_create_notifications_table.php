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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['relance', 'expiration', 'revision', 'maintenance', 'generale'])->default('generale');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained('contrats')->onDelete('cascade');
            $table->foreignId('bien_id')->nullable()->constrained('biens')->onDelete('cascade');
            $table->foreignId('document_id')->nullable()->constrained('documents')->onDelete('cascade');
            $table->string('titre');
            $table->text('message');
            $table->boolean('lue')->default(false);
            $table->boolean('envoyee_par_email')->default(false);
            $table->timestamp('lue_le')->nullable();
            $table->timestamp('envoyee_le')->nullable();
            $table->string('priorite')->default('normale'); // basse, normale, haute, urgente
            $table->json('metadata')->nullable(); // Données supplémentaires (montant, date, etc.)
            $table->timestamps();
            $table->softDeletes();

            // Index pour recherches rapides
            $table->index(['user_id', 'lue']);
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};