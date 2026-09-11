<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'id_tenant',
        'id_modulo',
        'clave',
        'nombre',
        'descripcion',
        'es_sistema',
    ];

    protected $casts = [
        'es_sistema' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'id_rol', 'id_permiso');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuarios::class, 'usuario_rol', 'id_rol', 'id_usuario');
    }

    public static function esAdminTenant(int $idUsuario, int $idTenant): bool
    {
        return DB::table('usuario_rol')
            ->join('roles', 'roles.id_rol', '=', 'usuario_rol.id_rol')
            ->where('usuario_rol.id_usuario', $idUsuario)
            ->where('usuario_rol.id_tenant', $idTenant)
            ->where('roles.clave', 'tenant.admin')
            ->exists();
    }

    public static function idsAdminTenant(int $idTenant): array
    {
        return DB::table('usuario_rol')
            ->join('roles', 'roles.id_rol', '=', 'usuario_rol.id_rol')
            ->where('usuario_rol.id_tenant', $idTenant)
            ->where('roles.clave', 'tenant.admin')
            ->pluck('usuario_rol.id_usuario')
            ->all();
    }

    public static function esSuperAdmin(int $idUsuario): bool
    {
        return DB::table('usuario_rol_global')
            ->join('roles', 'roles.id_rol', '=', 'usuario_rol_global.id_rol')
            ->where('usuario_rol_global.id_usuario', $idUsuario)
            ->where('roles.clave', 'platform.superadmin')
            ->exists();
    }

    public static function asignarTenantAdmin(int $idUsuario, int $idTenant, ?int $asignadoPor = null): void
    {
        $rol = self::firstOrCreate(
            ['id_tenant' => $idTenant, 'clave' => 'tenant.admin'],
            ['nombre' => 'Administrador', 'es_sistema' => true]
        );

        DB::table('usuario_rol')->updateOrInsert(
            ['id_usuario' => $idUsuario, 'id_tenant' => $idTenant, 'id_rol' => $rol->id_rol],
            ['asignado_por' => $asignadoPor, 'asignado_en' => now(), 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public static function asignarRol(int $idUsuario, int $idTenant, int $idRol, ?int $asignadoPor = null): void
    {
        DB::table('usuario_rol')->updateOrInsert(
            ['id_usuario' => $idUsuario, 'id_tenant' => $idTenant, 'id_rol' => $idRol],
            ['asignado_por' => $asignadoPor, 'asignado_en' => now(), 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public static function revocarTenantAdmin(int $idUsuario, int $idTenant): void
    {
        $rol = self::where('id_tenant', $idTenant)->where('clave', 'tenant.admin')->first();
        if (!$rol) {
            return;
        }

        DB::table('usuario_rol')
            ->where('id_usuario', $idUsuario)
            ->where('id_tenant', $idTenant)
            ->where('id_rol', $rol->id_rol)
            ->delete();
    }

    /**
     * ¿Tiene el usuario el permiso dado (clave "recurso.accion") en este tenant?
     * Los admins de tenant y el superadmin de plataforma siempre pasan.
     */
    public static function tienePermiso(int $idUsuario, int $idTenant, string $clave): bool
    {
        if (self::esAdminTenant($idUsuario, $idTenant) || self::esSuperAdmin($idUsuario)) {
            return true;
        }

        return DB::table('usuario_rol')
            ->join('rol_permiso', 'rol_permiso.id_rol', '=', 'usuario_rol.id_rol')
            ->join('permisos', 'permisos.id_permiso', '=', 'rol_permiso.id_permiso')
            ->where('usuario_rol.id_usuario', $idUsuario)
            ->where('usuario_rol.id_tenant', $idTenant)
            ->where('permisos.clave', $clave)
            ->exists();
    }

    /**
     * Todas las claves de permiso que tiene el usuario en este tenant
     * (vía los roles que tenga asignados). No incluye el bypass de admin.
     */
    public static function permisosDe(int $idUsuario, int $idTenant): array
    {
        return DB::table('usuario_rol')
            ->join('rol_permiso', 'rol_permiso.id_rol', '=', 'usuario_rol.id_rol')
            ->join('permisos', 'permisos.id_permiso', '=', 'rol_permiso.id_permiso')
            ->where('usuario_rol.id_usuario', $idUsuario)
            ->where('usuario_rol.id_tenant', $idTenant)
            ->distinct()
            ->pluck('permisos.clave')
            ->all();
    }

    /**
     * Asigna el rol "tenant.miembro" (todos los permisos, rol por defecto
     * para no-admins) a un usuario, creando el rol para el tenant si hace
     * falta. Usado al invitar un miembro nuevo o al aprovisionar un tenant.
     */
    public static function asignarMiembro(int $idUsuario, int $idTenant, ?int $asignadoPor = null): void
    {
        $rol = self::firstOrCreate(
            ['id_tenant' => $idTenant, 'clave' => 'tenant.miembro'],
            [
                'nombre' => 'Miembro',
                'descripcion' => 'Rol por defecto con todos los permisos.',
                'es_sistema' => true,
            ]
        );

        $idsPermisos = DB::table('permisos')->pluck('id_permiso');
        $existentes = DB::table('rol_permiso')->where('id_rol', $rol->id_rol)->pluck('id_permiso');
        foreach ($idsPermisos->diff($existentes) as $idPermiso) {
            DB::table('rol_permiso')->insert(['id_rol' => $rol->id_rol, 'id_permiso' => $idPermiso]);
        }

        DB::table('usuario_rol')->updateOrInsert(
            ['id_usuario' => $idUsuario, 'id_tenant' => $idTenant, 'id_rol' => $rol->id_rol],
            ['asignado_por' => $asignadoPor, 'asignado_en' => now(), 'created_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Crea (si hace falta) el rol de sistema "tenant.cocinero" -- único
     * permiso "erp_ventas.comandas" -- para este tenant. La migración que lo
     * introdujo ya lo sembró para tenants existentes; esto cubre tenants
     * creados después, llamado de forma perezosa desde RolController::index
     * para que siempre aparezca en el selector de roles sin necesitar otra
     * migración por cada tenant nuevo.
     */
    public static function firstOrCreateRolCocinero(int $idTenant): self
    {
        $rol = self::firstOrCreate(
            ['id_tenant' => $idTenant, 'clave' => 'tenant.cocinero'],
            [
                'nombre' => 'Cocinero',
                'descripcion' => 'Solo ve las comandas pendientes (cocina/bar) y las marca listas.',
                'es_sistema' => true,
            ]
        );

        $idPermiso = DB::table('permisos')->where('clave', 'erp_ventas.comandas')->value('id_permiso');
        if ($idPermiso) {
            DB::table('rol_permiso')->updateOrInsert(['id_rol' => $rol->id_rol, 'id_permiso' => $idPermiso], []);
        }

        return $rol;
    }

    /**
     * Crea (si hace falta) el rol de sistema "tenant.mantenimiento" -- único
     * permiso "erp_habitaciones.mantenimiento" -- para este tenant. Mismo
     * patrón que firstOrCreateRolCocinero(): la migración que lo introdujo
     * ya lo sembró para tenants existentes; esto cubre tenants creados
     * después, llamado de forma perezosa desde RolController::index.
     */
    public static function firstOrCreateRolMantenimiento(int $idTenant): self
    {
        $rol = self::firstOrCreate(
            ['id_tenant' => $idTenant, 'clave' => 'tenant.mantenimiento'],
            [
                'nombre' => 'Mantenimiento',
                'descripcion' => 'Solo ve los tickets de mantenimiento por habitación y los resuelve.',
                'es_sistema' => true,
            ]
        );

        $idPermiso = DB::table('permisos')->where('clave', 'erp_habitaciones.mantenimiento')->value('id_permiso');
        if ($idPermiso) {
            DB::table('rol_permiso')->updateOrInsert(['id_rol' => $rol->id_rol, 'id_permiso' => $idPermiso], []);
        }

        return $rol;
    }
}
