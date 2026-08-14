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
        Schema::create('erp_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_pedido')->constrained('erp_pedidos_venta', 'id')->cascadeOnDelete();
            $table->foreignId('id_usuario')->nullable()->constrained('usuarios', 'id_usuario')->nullOnDelete();

            $table->enum('tipo', ['interna', 'timbrada']);
            $table->enum('estado', ['registrada', 'pendiente_timbrado', 'timbrada', 'error', 'cancelada'])->default('registrada');

            $table->string('serie', 10)->default('A');
            $table->unsignedInteger('folio');

            // Datos fiscales del receptor (no reutilizamos el RFC del Cliente
            // porque puede diferir del receptor de esta factura puntual, ej.
            // facturar a nombre de otra razón social del mismo cliente).
            $table->string('rfc_receptor', 20);
            $table->string('razon_social_receptor', 150);
            $table->string('uso_cfdi', 10)->nullable();
            $table->string('forma_pago_sat', 5)->nullable();
            $table->string('metodo_pago_sat', 5)->nullable();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            // Solo se llenan si tipo=timbrada y el PAC responde exitosamente.
            $table->string('uuid', 60)->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('fecha_timbrado')->nullable();
            $table->text('error_mensaje')->nullable();

            $table->timestamps();

            $table->unique(['id_tenant', 'serie', 'folio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_facturas');
    }
};
