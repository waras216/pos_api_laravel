<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->string('destino', 150);
            $table->string('transportista', 100);
            $table->string('eta', 100);
            $table->enum('estado', ['en_transito', 'entregado'])->default('en_transito');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_envios');
    }
};
