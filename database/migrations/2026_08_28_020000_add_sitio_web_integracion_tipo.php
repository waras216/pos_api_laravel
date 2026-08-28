<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Nueva integración "sitio_web": permite al tenant capturar leads desde
     * un formulario en su propia landing page (WordPress o cualquier otra)
     * vía un endpoint público autenticado por token, ver
     * PublicFormularioController. El enum de `tipo` en Postgres es en
     * realidad un CHECK constraint (no un tipo ENUM nativo), así que se
     * recrea con el valor nuevo en vez de un ALTER TYPE.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE integraciones DROP CONSTRAINT integraciones_tipo_check');
        DB::statement("ALTER TABLE integraciones ADD CONSTRAINT integraciones_tipo_check CHECK (tipo IN ('whatsapp','email','calendario','almacenamiento','sitio_web','otro'))");

        // Backfill: los tenants ya existentes no pasan por
        // OnboardingService::provisionarTenantYUsuario de nuevo, así que no
        // reciben la fila sembrada automáticamente -- se crea acá con un
        // token nuevo para cada uno.
        $idsTenant = DB::table('tenants')->pluck('id_tenant');
        foreach ($idsTenant as $idTenant) {
            $yaExiste = DB::table('integraciones')->where('id_tenant', $idTenant)->where('tipo', 'sitio_web')->exists();
            if ($yaExiste) continue;

            DB::table('integraciones')->insert([
                'id_tenant' => $idTenant,
                'nombre' => 'Sitio Web',
                'tipo' => 'sitio_web',
                'estado' => 'desconectada',
                'configuracion' => json_encode(['token' => Str::random(40)]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('integraciones')->where('tipo', 'sitio_web')->delete();
        DB::statement('ALTER TABLE integraciones DROP CONSTRAINT integraciones_tipo_check');
        DB::statement("ALTER TABLE integraciones ADD CONSTRAINT integraciones_tipo_check CHECK (tipo IN ('whatsapp','email','calendario','almacenamiento','otro'))");
    }
};
