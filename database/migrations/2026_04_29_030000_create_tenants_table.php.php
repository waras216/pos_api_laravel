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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id('id_tenant');//AUTO INCREMENT PRIMARY KEY
            $table->string('nombre_tenant', 100);
            $table->string('subdominio')->unique();
            $table->string('estado')->default('activo');
            $table->foreignId('id_tiponegocio')
                ->constrained('negocios', 'id_tiponegocio')
                ->cascadeOnDelete();

            $table->foreignId('id_plan')
                ->constrained('plans', 'id_plan')
                ->cascadeOnDelete();
                    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
