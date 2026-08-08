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
        Schema::table('declaration_jours', function (Blueprint $table) {
            $table->enum('type_periode', ['quotidien', 'hebdomadaire', 'mensuel'])
                ->default('quotidien')
                ->after('date_repas');
            $table->date('date_debut')->after('type_periode');
            $table->date('date_fin')->after('date_debut');
            $table->json('repas')->after('date_fin');

            $table->unique(['beneficiaire_id', 'date_repas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaration_jours', function (Blueprint $table) {
            $table->dropUnique(['beneficiaire_id', 'date_repas']);
            $table->dropColumn(['type_periode', 'date_debut', 'date_fin', 'repas']);
        });
    }
};
