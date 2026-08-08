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
        Schema::create('derogations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('statut', ['demandee', 'autorisee', 'refusee'])->default('demandee');
            $table->text('motif')->nullable();
            $table->foreignId('demande_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('autorisee_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['service_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derogations');
    }
};
