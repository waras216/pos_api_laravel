<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_proyecto_horas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_proyecto')->constrained('erp_proyectos')->cascadeOnDelete();
            $table->string('colaborador', 150);
            $table->date('fecha');
            $table->decimal('horas', 5, 2);
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_proyecto_horas');
    }
};
