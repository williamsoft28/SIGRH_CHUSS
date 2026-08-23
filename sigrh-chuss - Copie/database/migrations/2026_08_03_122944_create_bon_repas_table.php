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
        Schema::create('bon_repas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_jour_id')->constrained()->cascadeOnDelete();
            $table->string('code_unique')->unique();
            $table->enum('type_periode', ['quotidien', 'hebdomadaire', 'mensuel']);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('canal_envoi', ['whatsapp', 'email', 'tiers']);
            $table->dateTime('date_emission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_repas');
    }
};
