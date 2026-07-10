<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('erp_pedidos_venta')->truncate();

        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->dropColumn('cliente');
            $table->foreignId('id_cliente')->after('id_tenant')->constrained('clientes', 'id_cliente');
        });

        DB::statement("ALTER TABLE erp_pedidos_venta MODIFY estado ENUM('pendiente', 'enviado', 'facturado', 'cancelada') DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE erp_pedidos_venta MODIFY estado ENUM('pendiente', 'enviado', 'facturado') DEFAULT 'pendiente'");

        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_cliente');
            $table->string('cliente', 150)->after('id_tenant');
        });
    }
};
