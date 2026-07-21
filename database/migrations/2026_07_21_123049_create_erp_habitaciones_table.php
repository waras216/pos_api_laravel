<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_habitaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->string('tipo', 20)->default('Sgl');
            $table->unsignedInteger('piso')->default(1);
            $table->string('estado')->default('libre');
            $table->string('huesped')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->unsignedInteger('noches')->nullable();
            $table->timestamps();

            $table->unique(['id_tenant', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_habitaciones');
    }
};
