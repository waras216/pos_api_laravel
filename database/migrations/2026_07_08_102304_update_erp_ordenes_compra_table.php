<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('erp_ordenes_compra')->truncate();

        Schema::table('erp_ordenes_compra', function (Blueprint $table) {
            $table->dropColumn(['proveedor', 'items']);
            $table->foreignId('id_proveedor')->after('id_tenant')->constrained('proveedores', 'id_proveedor');
        });
    }

    public function down(): void
    {
        Schema::table('erp_ordenes_compra', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_proveedor');
            $table->string('proveedor', 150)->after('id_tenant');
            $table->integer('items')->default(0);
        });
    }
};
