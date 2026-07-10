<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_producto')->constrained('productos', 'id_productos');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->string('motivo', 30);
            $table->string('referencia', 100)->nullable();
            $table->integer('stock_resultante');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_movimientos_stock');
    }
};
