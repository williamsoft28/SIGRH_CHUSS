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
        Schema::create('repas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_jour_id')->constrained('menu_jours')->cascadeOnDelete();
            $table->enum('type_repas', ['petit_dejeuner', 'dejeuner', 'diner']);
            $table->foreignId('plat_id')->nullable()->constrained('plats')->nullOnDelete();
            $table->foreignId('sauce_id')->nullable()->constrained('sauces')->nullOnDelete();
            $table->foreignId('viande_id')->nullable()->constrained('viandes')->nullOnDelete();
            $table->foreignId('dessert_id')->nullable()->constrained('plats')->nullOnDelete();
            $table->timestamps();

            $table->unique(['menu_jour_id', 'type_repas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repas');
    }
};
