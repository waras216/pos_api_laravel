<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_movimientos', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('erp_envios', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('erp_movimientos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('erp_envios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
