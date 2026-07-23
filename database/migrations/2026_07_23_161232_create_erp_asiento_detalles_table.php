<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_asiento_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asiento')->constrained('erp_asientos')->cascadeOnDelete();
            $table->foreignId('id_cuenta')->constrained('erp_plan_cuentas');
            $table->decimal('debe', 14, 2)->default(0);
            $table->decimal('haber', 14, 2)->default(0);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_asiento_detalles');
    }
};
