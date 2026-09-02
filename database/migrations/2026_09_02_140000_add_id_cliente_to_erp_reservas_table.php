<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_reservas', function (Blueprint $table) {
            $table->foreignId('id_cliente')->nullable()->after('huesped')->constrained('clientes', 'id_cliente')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('erp_reservas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_cliente');
        });
    }
};
