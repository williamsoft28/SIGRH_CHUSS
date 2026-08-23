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
        Schema::create('zone_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->onDelete('cascade');
            $table->date('date_service');
            $table->enum('statut', ['en_attente', 'servi', 'non_servi'])->default('en_attente');
            $table->time('heure_service')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
            
            // Une zone ne peut être enregistrée qu'une seule fois par jour
            $table->unique(['zone_id', 'date_service']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_services');
    }
};
