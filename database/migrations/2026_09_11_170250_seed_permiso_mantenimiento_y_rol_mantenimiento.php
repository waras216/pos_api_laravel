<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CLAVE_PERMISO = 'erp_habitaciones.mantenimiento';

    /**
     * "Mantenimiento" es un rol de sistema (mismo patrón que "Cocinero", ver
     * seed_permiso_comandas_y_rol_cocinero) con un único permiso granular
     * nuevo -- ver y resolver los tickets de mantenimiento por habitación
     * (POS > Mantenimiento) -- sin el resto de erp_habitaciones/erp_ventas
     * (no puede cobrar, hacer check-in/out, ni editar habitaciones). Se
     * siembra para tenants existentes; para tenants nuevos lo crea
     * Rol::firstOrCreateRolMantenimiento() la primera vez que un admin entra
     * a Configuración → Equipo (igual que el Cocinero).
     */
    public function up(): void
    {
        DB::table('permisos')->updateOrInsert(
            ['clave' => self::CLAVE_PERMISO],
            [
                'descripcion' => 'Ver y resolver los tickets de mantenimiento por habitación',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $idPermiso = DB::table('permisos')->where('clave', self::CLAVE_PERMISO)->value('id_permiso');

        $tenantIds = DB::table('tenants')->pluck('id_tenant');

        foreach ($tenantIds as $idTenant) {
            // tenant.miembro tiene "todos los permisos" por convención -- sincronizar
            // este nuevo permiso ahí también (ver seed_permiso_comandas_y_rol_cocinero).
            $idRolMiembro = DB::table('roles')->where('id_tenant', $idTenant)->where('clave', 'tenant.miembro')->value('id_rol');
            if ($idRolMiembro) {
                DB::table('rol_permiso')->updateOrInsert(['id_rol' => $idRolMiembro, 'id_permiso' => $idPermiso], []);
            }

            $idRolMantenimiento = DB::table('roles')->where('id_tenant', $idTenant)->where('clave', 'tenant.mantenimiento')->value('id_rol');
            if (! $idRolMantenimiento) {
                $idRolMantenimiento = DB::table('roles')->insertGetId([
                    'id_tenant' => $idTenant,
                    'id_modulo' => null,
                    'clave' => 'tenant.mantenimiento',
                    'nombre' => 'Mantenimiento',
                    'descripcion' => 'Solo ve los tickets de mantenimiento por habitación y los resuelve.',
                    'es_sistema' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_rol');
            }
            DB::table('rol_permiso')->updateOrInsert(['id_rol' => $idRolMantenimiento, 'id_permiso' => $idPermiso], []);
        }
    }

    public function down(): void
    {
        $idsRolesMantenimiento = DB::table('roles')->where('clave', 'tenant.mantenimiento')->pluck('id_rol');
        DB::table('usuario_rol')->whereIn('id_rol', $idsRolesMantenimiento)->delete();
        DB::table('rol_permiso')->whereIn('id_rol', $idsRolesMantenimiento)->delete();
        DB::table('roles')->where('clave', 'tenant.mantenimiento')->delete();

        $idPermiso = DB::table('permisos')->where('clave', self::CLAVE_PERMISO)->value('id_permiso');
        if ($idPermiso) {
            DB::table('rol_permiso')->where('id_permiso', $idPermiso)->delete();
            DB::table('permisos')->where('id_permiso', $idPermiso)->delete();
        }
    }
};
