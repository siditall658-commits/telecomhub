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
    Schema::create('telecom_services', function (Blueprint $table) {
        $table->id();
        // Ici on lie le service au client
        $table->foreignId('client_id')->constrained()->onDelete('cascade');
        $table->string('type_service'); // Fibre, ADSL, Mobile, etc.
        $table->string('numero_ligne')->unique();
        $table->enum('statut', ['actif', 'suspendu', 'en_panne'])->default('actif');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telecomservices');
    }
};
