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
        Schema::table('oportunidades', function (Blueprint $table) {
            $table->enum('etapa', ['prospeccion', 'contacto', 'propuesta', 'negociacion', 'cierre'])
                ->default('prospeccion')
                ->after('probabilidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oportunidades', function (Blueprint $table) {
            $table->dropColumn('etapa');
        });
    }
};
