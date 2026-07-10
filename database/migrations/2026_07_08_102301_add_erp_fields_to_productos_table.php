<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('codigo_barrera');
            $table->string('sku', 100)->nullable()->after('descripcion');
            $table->integer('stock_minimo')->default(0)->after('stock');
            $table->decimal('precio_compra', 10, 2)->default(0)->after('precio');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock_minimo', 'precio_compra']);
            $table->string('codigo_barrera', 100)->nullable();
        });
    }
};
