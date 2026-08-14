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
        // Marca si ya se disparó la automatización "actividad_vencida" para
        // esta fila -- sin esto, el comando programado (ver routes/console.php)
        // la volvería a disparar en cada corrida mientras siga vencida.
        Schema::table('actividades', function (Blueprint $table) {
            $table->boolean('automatizacion_disparada')->default(false)->after('fecha_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('automatizacion_disparada');
        });
    }
};
