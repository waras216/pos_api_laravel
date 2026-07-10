<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido')->constrained('erp_pedidos_venta')->cascadeOnDelete();
            $table->foreignId('id_producto')->constrained('productos', 'id_productos');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_pedido_items');
    }
};
