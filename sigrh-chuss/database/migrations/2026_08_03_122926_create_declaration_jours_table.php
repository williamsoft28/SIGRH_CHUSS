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
        Schema::create('declaration_jours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sus_id')->constrained('sus')->cascadeOnDelete();
            $table->foreignId('beneficiaire_id')->constrained()->cascadeOnDelete();
            $table->date('date_repas');
            $table->time('heure_limite')->default('09:00:00');
            $table->enum('statut', ['en_saisie', 'verrouillee', 'validee']);
            $table->boolean('deroge')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaration_jours');
    }
};
