<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_solicitudes_huesped', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_habitacion')->constrained('erp_habitaciones')->cascadeOnDelete();
            $table->string('huesped', 150)->nullable();
            $table->string('titulo', 150);
            $table->string('descripcion', 500)->nullable();
            $table->string('categoria', 15)->default('solicitud');
            $table->string('prioridad', 10)->default('media');
            $table->string('estado', 15)->default('abierta');
            $table->timestamp('resuelta_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_solicitudes_huesped');
    }
};
