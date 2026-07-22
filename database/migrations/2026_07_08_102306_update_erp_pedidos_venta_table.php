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

        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->string('estado', 30)->default('pendiente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->string('estado', 30)->default('pendiente')->change();
        });

        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_cliente');
            $table->string('cliente', 150)->after('id_tenant');
        });
    }
};
