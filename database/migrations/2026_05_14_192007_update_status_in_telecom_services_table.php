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
    Schema::table('telecom_services', function (Blueprint $table) {
        // On change la colonne pour qu'elle accepte plus de texte
        $table->string('statut')->default('actif')->change();
    });
}

public function down(): void
{
    Schema::table('telecom_services', function (Blueprint $table) {
        $table->string('statut', 10)->change();
    });
}
};
