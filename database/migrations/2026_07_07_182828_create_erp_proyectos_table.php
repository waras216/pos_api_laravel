<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('cliente', 150);
            $table->string('responsable', 150);
            $table->enum('estado', ['activo', 'pausado', 'completado'])->default('activo');
            $table->unsignedTinyInteger('progreso')->default(0);
            $table->integer('horas')->default(0);
            $table->decimal('presupuesto', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_proyectos');
    }
};
