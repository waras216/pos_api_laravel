<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_plan_cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('codigo', 20);
            $table->string('nombre', 150);
            $table->enum('tipo', ['activo', 'pasivo', 'capital', 'ingreso', 'costo', 'gasto']);
            $table->enum('naturaleza', ['deudora', 'acreedora']);
            $table->foreignId('id_cuenta_padre')->nullable()->constrained('erp_plan_cuentas')->nullOnDelete();
            $table->boolean('es_movible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['id_tenant', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_plan_cuentas');
    }
};
