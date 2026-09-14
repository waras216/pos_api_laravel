<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->enum('tipo', ['porcentaje', 'monto_fijo'])->default('porcentaje');
            $table->decimal('valor', 10, 2);
            $table->foreignId('id_producto')->nullable()->constrained('productos', 'id_productos')->nullOnDelete();
            $table->foreignId('id_categorias')->nullable()->constrained('categorias', 'id_categoria')->nullOnDelete();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_promociones');
    }
};
