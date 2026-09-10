<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue "enviada a cocina/barra" (estado='enviada') de "ya la preparó
     * el bartender/cocinero" -- necesario para que la pantalla de comandas
     * pendientes (KDS) sepa qué sigue en cola y qué ya está lista para que
     * el mesero la recoja, sin inventar un cuarto valor de `estado`.
     */
    public function up(): void
    {
        Schema::table('erp_comandas', function (Blueprint $table) {
            $table->boolean('preparada')->default(false)->after('enviada_cocina');
        });
    }

    public function down(): void
    {
        Schema::table('erp_comandas', function (Blueprint $table) {
            $table->dropColumn('preparada');
        });
    }
};
