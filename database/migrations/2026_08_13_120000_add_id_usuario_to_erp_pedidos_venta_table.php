<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->foreignId('id_usuario')->nullable()->after('id_cliente')
                ->constrained('usuarios', 'id_usuario')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('erp_pedidos_venta', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_usuario');
        });
    }
};
