<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_orden_compra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_orden_compra')->constrained('erp_ordenes_compra')->cascadeOnDelete();
            $table->foreignId('id_producto')->constrained('productos', 'id_productos');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_orden_compra_items');
    }
};
