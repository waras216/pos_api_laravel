<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Mismo patrón que 2026_07_16_170000_seed_permisos_y_backfill_roles: agrega
// los recursos nuevos de la sección "Tienda / Sucursal" y los sincroniza al
// rol tenant.miembro de cada tenant existente (tenant.admin ya bypassa el
// chequeo de permisos por completo, ver Rol::tienePermiso).
return new class extends Migration
{
    private const RECURSOS = [
        'erp_sucursales', 'erp_cajas', 'erp_transferencias',
        'erp_promociones', 'erp_devoluciones', 'erp_creditos',
    ];

    private const ACCIONES = ['ver', 'crear', 'editar', 'eliminar'];

    public function up(): void
    {
        foreach (self::RECURSOS as $recurso) {
            foreach (self::ACCIONES as $accion) {
                DB::table('permisos')->updateOrInsert(
                    ['clave' => "{$recurso}.{$accion}"],
                    [
                        'descripcion' => ucfirst($accion) . ' ' . str_replace('_', ' ', $recurso),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        $idsPermisos = DB::table('permisos')
            ->where(function ($q) {
                foreach (self::RECURSOS as $recurso) {
                    $q->orWhere('clave', 'like', "{$recurso}.%");
                }
            })
            ->pluck('id_permiso');

        $idsRolesMiembro = DB::table('roles')->where('clave', 'tenant.miembro')->pluck('id_rol');
        foreach ($idsRolesMiembro as $idRol) {
            $existentes = DB::table('rol_permiso')->where('id_rol', $idRol)->pluck('id_permiso')->all();
            foreach (array_diff($idsPermisos->all(), $existentes) as $idPermiso) {
                DB::table('rol_permiso')->insert(['id_rol' => $idRol, 'id_permiso' => $idPermiso]);
            }
        }
    }

    public function down(): void
    {
        $claves = [];
        foreach (self::RECURSOS as $recurso) {
            foreach (self::ACCIONES as $accion) {
                $claves[] = "{$recurso}.{$accion}";
            }
        }
        DB::table('permisos')->whereIn('clave', $claves)->delete();
    }
};
