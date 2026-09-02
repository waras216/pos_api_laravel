<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_habitacion')->constrained('erp_habitaciones')->cascadeOnDelete();
            $table->string('huesped');
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_checkin');
            $table->date('fecha_checkout');
            $table->unsignedInteger('noches');
            $table->string('notas', 255)->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_reservas');
    }
};
