<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('action', [
                'created',
                'updated',
                'downloaded',
                'viewed',
                'deleted',
                'shared',
                'unshared',
                'uploaded',
                'regenerated',
                'genere',
                'envoye',
                'partage',
                'telecharge',
                'relance',
                'paye'
            ]);
            $table->string('destinataire')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_logs');
    }
};
