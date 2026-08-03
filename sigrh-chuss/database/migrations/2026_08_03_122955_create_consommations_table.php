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
        Schema::create('consommations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_repas_id')->constrained('bon_repas')->cascadeOnDelete();
            $table->enum('type_repas', ['petit_dejeuner', 'dejeuner', 'diner']);
            $table->date('date_repas');
            $table->dateTime('date_heure_scan');
            $table->enum('statut', ['consomme', 'refuse']);
            $table->timestamps();

            $table->unique(['bon_repas_id', 'type_repas', 'date_repas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consommations');
    }
};
