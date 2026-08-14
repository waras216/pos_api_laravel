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
        // 'evento' y 'accion' pasan de texto libre (nunca interpretado por
        // nada) a valores de un catálogo cerrado validado en
        // AutomatizacionController — 'parametros' guarda la config
        // específica de cada acción (asunto/mensaje, título de actividad,
        // valor destino, usuario a notificar, etc.), ver AutomatizacionEngine.
        Schema::table('automatizaciones', function (Blueprint $table) {
            $table->json('parametros')->nullable()->after('accion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automatizaciones', function (Blueprint $table) {
            $table->dropColumn('parametros');
        });
    }
};
