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
        // Datos del emisor para CFDI (SAT México): se piden por separado del
        // resto del onboarding porque son opcionales — un tenant que solo
        // usa "registro interno" de facturas (ver FacturaService) nunca los
        // necesita, y forzarlos en el alta rompería el flujo para esos casos.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('rfc_emisor', 20)->nullable()->after('datos_nicho');
            $table->string('razon_social_emisor', 150)->nullable()->after('rfc_emisor');
            $table->string('regimen_fiscal_emisor', 5)->nullable()->after('razon_social_emisor');
            $table->string('codigo_postal_emisor', 10)->nullable()->after('regimen_fiscal_emisor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['rfc_emisor', 'razon_social_emisor', 'regimen_fiscal_emisor', 'codigo_postal_emisor']);
        });
    }
};
