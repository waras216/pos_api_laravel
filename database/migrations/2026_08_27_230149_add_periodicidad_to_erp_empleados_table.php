<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_empleados', function (Blueprint $table) {
            $table->enum('periodicidad', ['semanal', 'quincenal', 'mensual'])
                ->default('mensual')
                ->after('salario');
        });
    }

    public function down(): void
    {
        Schema::table('erp_empleados', function (Blueprint $table) {
            $table->dropColumn('periodicidad');
        });
    }
};
