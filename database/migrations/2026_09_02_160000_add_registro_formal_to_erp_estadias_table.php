<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_estadias', function (Blueprint $table) {
            $table->string('documento_tipo', 20)->nullable()->after('id_cliente');
            $table->string('documento_numero', 50)->nullable()->after('documento_tipo');
            $table->string('firma')->nullable()->after('documento_numero');
            $table->timestamp('firmado_at')->nullable()->after('firma');
        });
    }

    public function down(): void
    {
        Schema::table('erp_estadias', function (Blueprint $table) {
            $table->dropColumn(['documento_tipo', 'documento_numero', 'firma', 'firmado_at']);
        });
    }
};
