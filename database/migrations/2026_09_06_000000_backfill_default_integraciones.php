<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill: igual que la migración de "sitio_web"
     * (2026_08_28_020000_add_sitio_web_integracion_tipo.php), los tenants
     * creados con un INSERT directo (MasterSeeder, o cualquier tenant que ya
     * existía antes de que OnboardingService::provisionarTenantYUsuario
     * sembrara este catálogo) nunca pasan por ese método, así que se quedan
     * sin las 4 filas restantes (whatsapp, email, calendario, almacenamiento)
     * -- solo "sitio_web" fue backfilleado en su momento. Se completan aquí
     * para que la pantalla de Integraciones muestre el catálogo completo en
     * cualquier tenant.
     */
    public function up(): void
    {
        $catalogo = [
            ['nombre' => 'WhatsApp Business', 'tipo' => 'whatsapp'],
            ['nombre' => 'Email Marketing', 'tipo' => 'email'],
            ['nombre' => 'Google Calendar', 'tipo' => 'calendario'],
            ['nombre' => 'Almacenamiento en la nube', 'tipo' => 'almacenamiento'],
        ];

        $idsTenant = DB::table('tenants')->pluck('id_tenant');

        foreach ($idsTenant as $idTenant) {
            $tiposExistentes = DB::table('integraciones')
                ->where('id_tenant', $idTenant)
                ->pluck('tipo')
                ->all();

            foreach ($catalogo as $integracion) {
                if (in_array($integracion['tipo'], $tiposExistentes, true)) {
                    continue;
                }

                DB::table('integraciones')->insert([
                    'id_tenant' => $idTenant,
                    'nombre' => $integracion['nombre'],
                    'tipo' => $integracion['tipo'],
                    'estado' => 'desconectada',
                    'configuracion' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Irreversible a propósito: no hay forma de distinguir después del hecho
     * las filas que este backfill creó de las que ya sembró
     * OnboardingService al registrar el tenant, así que un `down()` que
     * borre por tipo eliminaría también integraciones reales creadas normal.
     */
    public function down(): void
    {
        //
    }
};
