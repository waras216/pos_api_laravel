<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cajeros con login por PIN no tienen correo. Postgres trata cada NULL
     * como distinto en un índice unique, así que esto no rompe el unique
     * existente sobre email aunque haya varios usuarios sin correo.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE usuarios ALTER COLUMN email DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE usuarios ALTER COLUMN email SET NOT NULL');
    }
};
