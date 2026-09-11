<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CLAVE_PERMISO = 'erp_ventas.comandas';

    /**
     * "Cocinero" es un rol de sistema (como Administrador/Miembro) con un
     * único permiso granular nuevo -- ver comandas pendientes y marcarlas
     * listas -- sin el resto de erp_ventas.* (no puede cobrar, cancelar
     * ventas, ni editar mesas). Se siembra para tenants existentes; para
     * tenants nuevos lo crea Rol::asignarCocinero() la primera vez que un
     * admin asigna el rol desde Configuración → Equipo.
     */
    public function up(): void
    {
        DB::table('permisos')->updateOrInsert(
            ['clave' => self::CLAVE_PERMISO],
            [
                'descripcion' => 'Ver y marcar listas las comandas de cocina/bar',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $idPermiso = DB::table('permisos')->where('clave', self::CLAVE_PERMISO)->value('id_permiso');

        $tenantIds = DB::table('tenants')->pluck('id_tenant');

        foreach ($tenantIds as $idTenant) {
            // El rol tenant.miembro tiene "todos los permisos" por convención
            // (ver seed_permisos_y_backfill_roles) -- sincronizar este nuevo
            // permiso ahí también, para que nadie con acceso completo pierda
            // la capacidad de marcar comandas.
            $idRolMiembro = DB::table('roles')->where('id_tenant', $idTenant)->where('clave', 'tenant.miembro')->value('id_rol');
            if ($idRolMiembro) {
                DB::table('rol_permiso')->updateOrInsert(['id_rol' => $idRolMiembro, 'id_permiso' => $idPermiso], []);
            }

            $idRolCocinero = DB::table('roles')->where('id_tenant', $idTenant)->where('clave', 'tenant.cocinero')->value('id_rol');
            if (! $idRolCocinero) {
                $idRolCocinero = DB::table('roles')->insertGetId([
                    'id_tenant' => $idTenant,
                    'id_modulo' => null,
                    'clave' => 'tenant.cocinero',
                    'nombre' => 'Cocinero',
                    'descripcion' => 'Solo ve las comandas pendientes (cocina/bar) y las marca listas.',
                    'es_sistema' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_rol');
            }
            DB::table('rol_permiso')->updateOrInsert(['id_rol' => $idRolCocinero, 'id_permiso' => $idPermiso], []);
        }
    }

    public function down(): void
    {
        $idsRolesCocinero = DB::table('roles')->where('clave', 'tenant.cocinero')->pluck('id_rol');
        DB::table('usuario_rol')->whereIn('id_rol', $idsRolesCocinero)->delete();
        DB::table('rol_permiso')->whereIn('id_rol', $idsRolesCocinero)->delete();
        DB::table('roles')->where('clave', 'tenant.cocinero')->delete();

        $idPermiso = DB::table('permisos')->where('clave', self::CLAVE_PERMISO)->value('id_permiso');
        if ($idPermiso) {
            DB::table('rol_permiso')->where('id_permiso', $idPermiso)->delete();
            DB::table('permisos')->where('id_permiso', $idPermiso)->delete();
        }
    }
};
