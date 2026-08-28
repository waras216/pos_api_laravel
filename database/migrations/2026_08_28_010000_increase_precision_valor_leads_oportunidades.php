<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * decimal(10,2) tope en ~$99,999,999.99 -- un valor estimado de venta
     * grande (ej. un contrato corporativo de 9 cifras) desbordaba la columna
     * y tiraba un 500 crudo de Postgres en vez de un error de validación.
     * Se sube a decimal(14,2) (tope ~$999,999,999,999.99), mismo orden de
     * magnitud que ya usan los "total"/"monto" del módulo ERP.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE leads ALTER COLUMN valor_estimado TYPE decimal(14,2)');
        DB::statement('ALTER TABLE oportunidades ALTER COLUMN valor TYPE decimal(14,2)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE leads ALTER COLUMN valor_estimado TYPE decimal(10,2)');
        DB::statement('ALTER TABLE oportunidades ALTER COLUMN valor TYPE decimal(10,2)');
    }
};
