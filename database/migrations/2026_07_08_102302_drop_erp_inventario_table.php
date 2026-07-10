<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('erp_inventario');
    }

    public function down(): void
    {
        Schema::create('erp_inventario', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('sku', 100);
            $table->string('categoria', 100)->default('General');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
