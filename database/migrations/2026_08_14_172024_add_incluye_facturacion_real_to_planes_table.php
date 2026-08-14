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
        // Un CFDI real le cuesta al PAC cobrarnos por timbre, a diferencia
        // del resto del ERP que no tiene costo variable — por eso el
        // timbrado real queda gateado por plan en vez de venir incluido
        // gratis en todos (ver FacturaService::crear).
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('incluye_facturacion_real')->default(false)->after('max_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('incluye_facturacion_real');
        });
    }
};
