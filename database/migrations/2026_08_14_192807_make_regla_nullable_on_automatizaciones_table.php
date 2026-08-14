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
        // 'regla' pasa de ser una condición de texto libre (nunca evaluada
        // por nada, ver AutomatizacionEngine) a una nota descriptiva
        // opcional -- ya no tiene sentido exigirla.
        Schema::table('automatizaciones', function (Blueprint $table) {
            $table->string('regla')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automatizaciones', function (Blueprint $table) {
            $table->string('regla')->nullable(false)->change();
        });
    }
};
