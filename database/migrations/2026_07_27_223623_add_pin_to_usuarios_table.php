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
        Schema::table('usuarios', function (Blueprint $table) {
            // Hash del PIN numérico para login rápido de cajeros (login sin
            // correo, en un dispositivo/terminal que ya tiene un tenant
            // recordado). Null = este usuario no tiene login por PIN activo.
            $table->string('pin')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
