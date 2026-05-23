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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes', 'id_cliente')->nullOnDelete();
            $table->foreignId('id_lead')->nullable()->constrained('leads', 'id_lead')->nullOnDelete();
            $table->foreignId('id_oportunidad')->nullable()->constrained('oportunidades', 'id_oportunidad')->nullOnDelete();
            $table->enum('tipo', ['llamada', 'reunion', 'email', 'tarea', 'nota'])->default('tarea');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
