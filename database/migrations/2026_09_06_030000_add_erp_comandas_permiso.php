<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CLAVES = ['erp_comandas.ver', 'erp_comandas.editar'];

    /**
     * Recurso de permiso propio para la pantalla de comandas pendientes
     * (bartender/cocinero) -- antes vivía bajo "erp_ventas", que también
     * cubre pedidos/cobros y demás Ventas del ERP. Separarlo permite un rol
     * "Bartender" limitado exclusivamente a ver/marcar comandas, sin acceso
     * al resto de Ventas (ver MesaController::pendientes/marcarPreparada).
     *
     * Igual que 2026_07_16_170000_seed_permisos_y_backfill_roles: se sincroniza
     * a "tenant.miembro" (el rol por defecto con todos los permisos) en cada
     * tenant para que ningún miembro existente pierda acceso al cambiar la
     * clave de permiso que protege estas dos rutas.
     */
    public function up(): void
    {
        foreach (self::CLAVES as $clave) {
            [$recurso, $accion] = explode('.', $clave);
            DB::table('permisos')->updateOrInsert(
                ['clave' => $clave],
                [
                    'descripcion' => ucfirst($accion) . ' ' . str_replace('_', ' ', $recurso),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $idsPermisos = DB::table('permisos')->whereIn('clave', self::CLAVES)->pluck('id_permiso');

        $idsRolMiembro = DB::table('roles')->where('clave', 'tenant.miembro')->pluck('id_rol');
        foreach ($idsRolMiembro as $idRol) {
            $existentes = DB::table('rol_permiso')->where('id_rol', $idRol)->pluck('id_permiso');
            foreach ($idsPermisos->diff($existentes) as $idPermiso) {
                DB::table('rol_permiso')->insert(['id_rol' => $idRol, 'id_permiso' => $idPermiso]);
            }
        }
    }

    public function down(): void
    {
        $idsPermisos = DB::table('permisos')->whereIn('clave', self::CLAVES)->pluck('id_permiso');
        DB::table('rol_permiso')->whereIn('id_permiso', $idsPermisos)->delete();
        DB::table('permisos')->whereIn('clave', self::CLAVES)->delete();
    }
};
