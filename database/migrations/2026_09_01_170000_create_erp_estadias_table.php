<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_estadias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_habitacion')->constrained('erp_habitaciones')->cascadeOnDelete();
            $table->string('huesped');
            $table->foreignId('id_cliente')->nullable()->constrained('clientes', 'id_cliente')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out_programado')->nullable();
            $table->unsignedInteger('noches')->nullable();
            $table->timestamp('check_out_real')->nullable();
            $table->decimal('total_hospedaje', 10, 2)->default(0);
            $table->decimal('total_consumos', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->foreignId('id_pedido')->nullable()->constrained('erp_pedidos_venta')->nullOnDelete();
            $table->string('estado', 20)->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_estadias');
    }
};
