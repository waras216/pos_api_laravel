<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_nomina_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('total', 14, 2);
            $table->foreignId('id_asiento')->constrained('erp_asientos');
            $table->timestamps();
        });

        Schema::create('erp_nomina_pago_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nomina_pago')->constrained('erp_nomina_pagos')->cascadeOnDelete();
            $table->foreignId('id_empleado')->constrained('erp_empleados');
            $table->decimal('salario', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_nomina_pago_detalles');
        Schema::dropIfExists('erp_nomina_pagos');
    }
};
