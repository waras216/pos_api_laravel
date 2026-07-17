<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_proyecto_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_proyecto')->constrained('erp_proyectos')->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada'])->default('pendiente');
            $table->string('asignado', 150)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_proyecto_tareas');
    }
};
