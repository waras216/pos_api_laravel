<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_productos');
            $table->foreignId('id_tenant')->constrained('tenants','id_tenant')->cascadeOnDelete();
            $table->foreignId('id_categorias')->constrained('categorias', 'id_categoria');
            $table->string('nombre',150);
            $table->string('descripcion', 350)->nullable();
            $table->decimal('precio', 10,2);
            $table->integer('stock')->default(0);
            $table->string('codigo_barrera', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
