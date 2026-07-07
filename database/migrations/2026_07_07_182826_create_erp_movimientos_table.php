<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('concepto', 200);
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('categoria', 100)->default('General');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_movimientos');
    }
};
