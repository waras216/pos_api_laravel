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

        // ->change() alters the column type but Postgres keeps the old CHECK
        // constraint the original ->enum() created (it's a separate object),
        // which would still reject 'cancelada'. MySQL has no such artifact.
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE erp_pedidos_venta DROP CONSTRAINT IF EXISTS erp_pedidos_venta_estado_check');
        }
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
