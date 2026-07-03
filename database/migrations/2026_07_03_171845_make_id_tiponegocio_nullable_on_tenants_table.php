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
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['id_tiponegocio']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tiponegocio')->nullable()->change();
            $table->foreign('id_tiponegocio')
                ->references('id_tiponegocio')->on('negocios')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['id_tiponegocio']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tiponegocio')->nullable(false)->change();
            $table->foreign('id_tiponegocio')
                ->references('id_tiponegocio')->on('negocios')
                ->cascadeOnDelete();
        });
    }
};
