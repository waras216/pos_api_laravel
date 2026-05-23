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
        Schema::create('oportunidades', function (Blueprint $table) {
            $table->id('id_oportunidad');
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente')->cascadeOnDelete();
            $table->foreignId('id_pipeline')->constrained('pipelines', 'id_pipeline')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
            $table->string('titulo',150);
            $table->decimal('valor', 10,2)->default(0);
            $table->integer('probabilidad')->default(0);
            $table->enum('estado', ['abierta', 'ganada', 'perdida'])->default('abierta');
            $table->date('fecha_cierre')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oportunidades');
    }
};
