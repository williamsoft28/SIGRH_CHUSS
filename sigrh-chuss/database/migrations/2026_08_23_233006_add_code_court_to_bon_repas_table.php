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
        Schema::table('bon_repas', function (Blueprint $table) {
            $table->string('code_court')->nullable()->unique()->after('code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bon_repas', function (Blueprint $table) {
            $table->dropColumn('code_court');
        });
    }
};
