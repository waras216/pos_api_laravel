<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const RECURSOS = [
        'categorias', 'productos', 'clientes', 'contactos', 'leads', 'pipelines',
        'oportunidades', 'actividades', 'marketing', 'automatizaciones', 'integraciones',
        'erp_inventario', 'erp_proveedores', 'erp_compras', 'erp_finanzas', 'erp_ventas',
        'erp_rrhh', 'erp_fabricacion', 'erp_scm', 'erp_proyectos',
    ];

    private const ACCIONES = ['ver', 'crear', 'editar', 'eliminar'];

    public function up(): void
    {
        $this->sembrarPermisos();
        $idsPermisos = DB::table('permisos')->pluck('id_permiso')->all();

        $tenantIds = DB::table('tenants')->pluck('id_tenant');

        foreach ($tenantIds as $idTenant) {
            $idRolMiembro = $this->firstOrCreateRolMiembro($idTenant);

            // Sincronizar TODOS los permisos al rol tenant.miembro (idempotente).
            $existentes = DB::table('rol_permiso')->where('id_rol', $idRolMiembro)->pluck('id_permiso')->all();
            $faltantes = array_diff($idsPermisos, $existentes);
            foreach ($faltantes as $idPermiso) {
                DB::table('rol_permiso')->insert(['id_rol' => $idRolMiembro, 'id_permiso' => $idPermiso]);
            }

            // Backfill: todo usuario del tenant que NO sea tenant.admin, y que
            // todavía no tenga una fila en usuario_rol para este tenant, se
            // asigna a tenant.miembro (con todos los permisos) para que nadie
            // pierda acceso al gatear las rutas en este mismo cambio.
            $idsAdmin = DB::table('usuario_rol')
                ->join('roles', 'roles.id_rol', '=', 'usuario_rol.id_rol')
                ->where('usuario_rol.id_tenant', $idTenant)
                ->where('roles.clave', 'tenant.admin')
                ->pluck('usuario_rol.id_usuario')
                ->all();

            $idsConRolEnTenant = DB::table('usuario_rol')
                ->where('id_tenant', $idTenant)
                ->pluck('id_usuario')
                ->all();

            $usuariosSinRol = DB::table('usuarios')
                ->where('id_tenant', $idTenant)
                ->whereNotIn('id_usuario', array_unique(array_merge($idsAdmin, $idsConRolEnTenant)))
                ->pluck('id_usuario');

            foreach ($usuariosSinRol as $idUsuario) {
                DB::table('usuario_rol')->insert([
                    'id_usuario' => $idUsuario,
                    'id_tenant' => $idTenant,
                    'id_rol' => $idRolMiembro,
                    'asignado_en' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $clavesPermisos = [];
        foreach (self::RECURSOS as $recurso) {
            foreach (self::ACCIONES as $accion) {
                $clavesPermisos[] = "{$recurso}.{$accion}";
            }
        }

        $idsRolesMiembro = DB::table('roles')->where('clave', 'tenant.miembro')->pluck('id_rol');
        DB::table('usuario_rol')->whereIn('id_rol', $idsRolesMiembro)->delete();
        DB::table('rol_permiso')->whereIn('id_rol', $idsRolesMiembro)->delete();
        DB::table('roles')->where('clave', 'tenant.miembro')->delete();
        DB::table('permisos')->whereIn('clave', $clavesPermisos)->delete();
    }

    private function sembrarPermisos(): void
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
    }

    private function firstOrCreateRolMiembro(int $idTenant): int
    {
        $rol = DB::table('roles')->where('id_tenant', $idTenant)->where('clave', 'tenant.miembro')->first();
        if ($rol) {
            return $rol->id_rol;
        }

        return DB::table('roles')->insertGetId([
            'id_tenant' => $idTenant,
            'id_modulo' => null,
            'clave' => 'tenant.miembro',
            'nombre' => 'Miembro',
            'descripcion' => 'Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
