<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_sucursal_origen')->constrained('erp_sucursales', 'id_sucursal');
            $table->foreignId('id_sucursal_destino')->constrained('erp_sucursales', 'id_sucursal');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario');
            $table->date('fecha');
            $table->enum('estado', ['pendiente', 'enviada', 'recibida', 'cancelada'])->default('pendiente');
            $table->string('notas', 350)->nullable();
            $table->timestamps();
        });

        Schema::create('erp_transferencia_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transferencia')->constrained('erp_transferencias')->cascadeOnDelete();
            $table->foreignId('id_producto')->constrained('productos', 'id_productos');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_transferencia_items');
        Schema::dropIfExists('erp_transferencias');
    }
};
