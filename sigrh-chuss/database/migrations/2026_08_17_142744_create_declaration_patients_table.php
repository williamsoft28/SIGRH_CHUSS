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
        Schema::create('declaration_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sus_id')->constrained('sus')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->date('date_repas');
            $table->foreignId('regime_special_id')->constrained('regime_specials')->cascadeOnDelete();
            $table->integer('nombre_plats')->default(0);
            $table->timestamps();
            
            // Un service ne peut avoir qu'une déclaration par régime et par jour (pour éviter les doublons accidentels).
            $table->unique(['service_id', 'date_repas', 'regime_special_id'], 'unique_declaration_patient_jour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaration_patients');
    }
};
