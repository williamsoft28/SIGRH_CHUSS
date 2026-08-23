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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('numero_semaine');
            $table->unsignedSmallInteger('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['soumis', 'en_observation', 'valide', 'applique', 'rejete']);
            $table->dateTime('date_soumission')->nullable();
            $table->dateTime('date_validation')->nullable();
            $table->unsignedInteger('nb_modifications')->default(0);
            $table->timestamps();

            $table->unique(['numero_semaine', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
