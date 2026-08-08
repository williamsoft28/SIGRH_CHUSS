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
        Schema::create('droit_repas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_repas_id')->constrained('bon_repas')->cascadeOnDelete();
            $table->date('date');
            $table->enum('type_repas', ['petit_dejeuner', 'dejeuner', 'diner']);
            $table->timestamps();

            $table->unique(['bon_repas_id', 'date', 'type_repas'], 'droit_repas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('droit_repas');
    }
};
