<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_mesas', function (Blueprint $table) {
            // Bar/Restaurante: a qué sección del hotel pertenece la mesa
            // (erp_mesas es compartida entre ambas, ver PosTerminalHotelMesasComponent).
            // Nula en mesas creadas antes de este campo o por un restaurante
            // independiente (nicho sin hotel, no aplica distinción).
            $table->string('seccion')->nullable()->after('capacidad');
            // El frontend (ErpMesasComponent/erp-service) ya enviaba estos dos
            // campos desde el inicio, pero la tabla nunca los tuvo -- store()
            // los descartaba en silencio. Se agregan para que realmente persistan.
            $table->string('ubicacion')->nullable()->after('seccion');
            $table->text('descripcion')->nullable()->after('ubicacion');
        });
    }

    public function down(): void
    {
        Schema::table('erp_mesas', function (Blueprint $table) {
            $table->dropColumn(['seccion', 'ubicacion', 'descripcion']);
        });
    }
};
