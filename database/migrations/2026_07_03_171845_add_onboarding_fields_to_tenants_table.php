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
            $table->string('moneda', 3)->nullable()->after('id_plan');
            $table->boolean('modulo_crm')->default(true)->after('moneda');
            $table->boolean('modulo_pos')->default(false)->after('modulo_crm');
            $table->boolean('modulo_erp')->default(false)->after('modulo_pos');
            $table->json('datos_nicho')->nullable()->after('modulo_erp');
            $table->boolean('onboarding_completado')->default(false)->after('datos_nicho');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'moneda',
                'modulo_crm',
                'modulo_pos',
                'modulo_erp',
                'datos_nicho',
                'onboarding_completado',
            ]);
        });
    }
};
