<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_pedido')->constrained('erp_pedidos_venta', 'id');
            $table->foreignId('id_producto')->constrained('productos', 'id_productos');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario');
            $table->integer('cantidad');
            $table->enum('tipo', ['devolucion', 'garantia'])->default('devolucion');
            $table->string('motivo', 250)->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'completada'])->default('pendiente');
            $table->decimal('monto_reembolso', 10, 2)->nullable();
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_devoluciones');
    }
};
