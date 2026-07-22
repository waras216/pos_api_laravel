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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('estado', 30)->default('nuevo')->change();
        });

        // ->change() alters the column type but Postgres keeps the old CHECK
        // constraint the original ->enum() created (it's a separate object),
        // which would still reject 'convertido'. MySQL has no such artifact.
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE leads DROP CONSTRAINT IF EXISTS leads_estado_check');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('estado', 30)->default('nuevo')->change();
        });
    }
};
