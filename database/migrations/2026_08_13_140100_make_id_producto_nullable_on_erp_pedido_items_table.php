<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_pedido_items', function (Blueprint $table) {
            $table->foreignId('id_producto')->nullable()->change();
            $table->string('descripcion', 150)->nullable()->after('id_producto');
        });
    }

    public function down(): void
    {
        Schema::table('erp_pedido_items', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->foreignId('id_producto')->nullable(false)->change();
        });
    }
};
