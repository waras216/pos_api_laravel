<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_nomina_pagos', function (Blueprint $table) {
            $table->enum('periodo', ['semanal', 'quincenal', 'mensual'])
                ->default('mensual')
                ->after('fecha');
            $table->date('fecha_inicio')->nullable()->after('periodo');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('erp_nomina_pagos', function (Blueprint $table) {
            $table->dropColumn(['periodo', 'fecha_inicio', 'fecha_fin']);
        });
    }
};
