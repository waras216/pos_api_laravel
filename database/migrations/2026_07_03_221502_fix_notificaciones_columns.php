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
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreignId('id_usuario')->after('id_tenant')
                ->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['id_cliente']);
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreignId('id_cliente')->nullable()->change();
            $table->foreign('id_cliente')
                ->references('id_cliente')->on('clientes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['id_cliente']);
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreignId('id_cliente')->nullable(false)->change();
            $table->foreign('id_cliente')
                ->references('id_cliente')->on('clientes')
                ->cascadeOnDelete();
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });
    }
};
