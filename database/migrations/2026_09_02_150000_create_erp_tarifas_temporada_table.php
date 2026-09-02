<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_tarifas_temporada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('tipo_ajuste', 15)->default('porcentaje');
            $table->decimal('valor', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_tarifas_temporada');
    }
};
