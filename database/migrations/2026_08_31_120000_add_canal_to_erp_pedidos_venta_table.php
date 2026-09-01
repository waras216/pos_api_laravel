<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->string('canal', 30)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->dropColumn('canal');
        });
    }
};
