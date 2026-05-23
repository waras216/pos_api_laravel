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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id('id_contacto');
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_cliente')->constrained('clientes','id_cliente')->cascadeOnDelete();
            $table->string('nombre',150);
            $table->string('apellido_p',150)->nullable();
            $table->string('apellido_m',150)->nullable();
            $table->string('email',200)->nullable();
            $table->string('telefono',20)->nullable();
            $table->string('cargo',100)->nullable();
            $table->boolean('principal')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
