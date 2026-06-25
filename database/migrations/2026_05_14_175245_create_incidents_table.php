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
    Schema::create('incidents', function (Blueprint $table) {
        $table->id();
        // On lie l'incident à un service spécifique (ex: la ligne Fibre de Ben)
        $table->foreignId('telecom_service_id')->constrained()->onDelete('cascade');
        $table->string('titre');
        $table->text('description');
        $table->string('priorite');
        $table->enum('statut', ['Ouvert', 'En cours', 'Résolu'])->default('Ouvert');
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
