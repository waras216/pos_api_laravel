<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_mesas', function (Blueprint $table) {
            $table->string('ubicacion', 100)->nullable()->after('capacidad');
            $table->string('descripcion', 255)->nullable()->after('ubicacion');
        });
    }

    public function down(): void
    {
        Schema::table('erp_mesas', function (Blueprint $table) {
            $table->dropColumn(['ubicacion', 'descripcion']);
        });
    }
};
