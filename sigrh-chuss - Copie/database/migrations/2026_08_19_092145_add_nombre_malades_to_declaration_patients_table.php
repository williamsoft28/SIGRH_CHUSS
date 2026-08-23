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
        Schema::table('declaration_patients', function (Blueprint $table) {
            $table->integer('nombre_malades')->default(0)->after('regime_special_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaration_patients', function (Blueprint $table) {
            $table->dropColumn('nombre_malades');
        });
    }
};
