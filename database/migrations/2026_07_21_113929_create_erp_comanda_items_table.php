<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_comanda_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_comanda')->constrained('erp_comandas')->cascadeOnDelete();
            $table->foreignId('id_producto')->nullable()->constrained('productos', 'id_productos')->nullOnDelete();
            $table->string('nombre', 150);
            $table->decimal('precio_unitario', 10, 2);
            $table->unsignedInteger('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_comanda_items');
    }
};
