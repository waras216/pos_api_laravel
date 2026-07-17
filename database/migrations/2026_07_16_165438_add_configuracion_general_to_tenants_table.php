<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('sector', 100)->nullable()->after('moneda');
            $table->string('idioma', 5)->nullable()->after('sector');
            $table->string('zona_horaria', 60)->nullable()->after('idioma');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['sector', 'idioma', 'zona_horaria']);
        });
    }
};
