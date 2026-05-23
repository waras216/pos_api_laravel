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
        Schema::create('leads', function (Blueprint $table) {
            $table->id('id_lead');
            $table->foreignId('id_tenant')->constrained('tenants','id_tenant')->cascadeOnDelete();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes','id_cliente')->nullOnDelete();
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();            
            $table->string('titulo',150);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['nuevo', 'contactado', 'calificado', 'perdido'])->default('nuevo');
            $table->enum('fuente', ['web', 'referido', 'llamada', 'email', 'otro'])->default('otro');
            $table->decimal('valor_estimado', 10,2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
