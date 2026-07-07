<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_ordenes_produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('producto', 150);
            $table->integer('cantidad')->default(0);
            $table->unsignedTinyInteger('progreso')->default(0);
            $table->enum('estado', ['en proceso', 'completada'])->default('en proceso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_ordenes_produccion');
    }
};
