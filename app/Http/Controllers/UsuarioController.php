<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\Rol;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $idsAdmin = Rol::idsAdminTenant($idTenant);

        // Roles no-admin por usuario (el de admin ya se muestra aparte con
        // es_admin), para poder mostrar un indicador de a qué tiene acceso.
        $rolesPorUsuario = DB::table('usuario_rol')
            ->join('roles', 'roles.id_rol', '=', 'usuario_rol.id_rol')
            ->where('usuario_rol.id_tenant', $idTenant)
            ->where('roles.clave', '!=', 'tenant.admin')
            ->select('usuario_rol.id_usuario', 'roles.nombre')
            ->get()
            ->groupBy('id_usuario');

        $usuarios = Membresia::where('membresias.id_tenant', $idTenant)
            ->where('membresias.estado', 'activa')
            ->join('usuarios', 'usuarios.id_usuario', '=', 'membresias.id_usuario')
            ->orderBy('usuarios.nombre')
            ->get([
                'usuarios.id_usuario',
                'usuarios.nombre',
                'usuarios.email',
                'usuarios.estado',
                'usuarios.created_at',
                'membresias.es_owner',
            ])
            ->map(function ($u) use ($idsAdmin, $rolesPorUsuario) {
                $data = $u->toArray();
                $data['es_admin'] = in_array($u->id_usuario, $idsAdmin);
                $data['roles'] = ($rolesPorUsuario[$u->id_usuario] ?? collect())->pluck('nombre')->values()->all();
                return $data;
            });

        return response()->json($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $tenant = $request->user()->tenant()->with('plan')->first();
        $max = $tenant->plan?->max_usuarios;
        $usuariosActivos = Membresia::where('id_tenant', $idTenant)->where('estado', 'activa')->count();

        if ($max !== null && $usuariosActivos >= $max) {
            return response()->json(['message' => 'Alcanzaste el límite de usuarios de tu plan'], 422);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            // Correo y contraseña ya no son obligatorios: un cajero puede
            // darse de alta solo con nombre + PIN, sin correo (entra por
            // /pin-login). Si sí se da correo, la contraseña es obligatoria.
            'email' => 'nullable|email',
            'password' => 'nullable|min:6',
            'es_admin' => 'sometimes|boolean',
            'id_rol' => ['nullable', 'integer', Rule::exists('roles', 'id_rol')->where('id_tenant', $idTenant)],
            'pin' => 'nullable|digits_between:4,8',
        ]);

        if (empty($data['email']) && empty($data['pin'])) {
            return response()->json(['message' => 'Ponle un correo o un PIN, si no este usuario no va a poder entrar'], 422);
        }
        if (! empty($data['email']) && empty($data['password'])) {
            return response()->json(['message' => 'La contraseña es obligatoria si le pones un correo'], 422);
        }

        $esAdmin = $data['es_admin'] ?? false;

        // Si el email ya pertenece a un usuario de STRATo, lo sumamos como
        // segunda membresía en vez de crear una identidad duplicada. Por
        // seguridad NO tocamos su nombre/password existentes solo porque
        // otro tenant lo esté invitando con datos distintos. Sin correo
        // (cajero) no hay nada que buscar: siempre es identidad nueva.
        $usuario = ! empty($data['email']) ? Usuarios::where('email', $data['email'])->first() : null;
        $cuentaExistente = (bool) $usuario;

        if ($usuario) {
            if (Membresia::where('id_usuario', $usuario->id_usuario)->where('id_tenant', $idTenant)->exists()) {
                return response()->json(['message' => 'Ese usuario ya pertenece a tu equipo'], 422);
            }
        } else {
            $usuario = Usuarios::create([
                'id_tenant' => $idTenant,
                'nombre' => $data['nombre'],
                'email' => $data['email'] ?? null,
                // Sin correo no hay password real que usar -- se guarda un
                // hash de algo aleatorio que nadie puede escribir, así el
                // login por PIN queda como única puerta de entrada.
                'password' => Hash::make($data['password'] ?? Str::random(40)),
                'pin' => ! empty($data['pin']) ? Hash::make($data['pin']) : null,
            ]);
        }

        Membresia::create([
            'id_usuario' => $usuario->id_usuario,
            'id_tenant' => $idTenant,
            'estado' => 'activa',
            'es_owner' => false,
            'invitado_por' => $request->user()->id_usuario,
            'unido_en' => now(),
        ]);

        if ($esAdmin) {
            Rol::asignarTenantAdmin($usuario->id_usuario, $idTenant, $request->user()->id_usuario);
        } elseif (! empty($data['id_rol'])) {
            Rol::asignarRol($usuario->id_usuario, $idTenant, $data['id_rol'], $request->user()->id_usuario);
        } else {
            // Sin rol elegido: rol por defecto (todos los permisos) para que
            // un miembro recién invitado no quede sin acceso a nada.
            Rol::asignarMiembro($usuario->id_usuario, $idTenant, $request->user()->id_usuario);
        }

        $respuesta = $usuario->toArray();
        unset($respuesta['es_superadmin']);
        $respuesta['es_admin'] = $esAdmin;
        $respuesta['cuenta_existente'] = $cuentaExistente;

        return response()->json($respuesta, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;

        Membresia::where('id_usuario', $id)->where('id_tenant', $idTenant)->firstOrFail();
        $usuario = Usuarios::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'password' => 'nullable|min:6',
            'pin' => 'nullable|digits_between:4,8',
            'es_admin' => 'sometimes|boolean',
            'estado' => 'sometimes|string|in:activo,ocupado,suspendido',
        ]);

        if (($data['estado'] ?? null) === 'suspendido' && $usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['message' => 'No puedes suspenderte a ti mismo'], 422);
        }

        $esUnicoAdmin = count(Rol::idsAdminTenant($idTenant)) <= 1;

        if (array_key_exists('es_admin', $data)
            && ! $data['es_admin']
            && $usuario->id_usuario === $request->user()->id_usuario
            && Rol::esAdminTenant($usuario->id_usuario, $idTenant)
            && $esUnicoAdmin) {
            return response()->json(['message' => 'No puedes quitarte el rol de admin, eres el único administrador'], 422);
        }

        $esAdminSolicitado = array_key_exists('es_admin', $data) ? $data['es_admin'] : null;
        unset($data['es_admin']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (! empty($data['pin'])) {
            $data['pin'] = Hash::make($data['pin']);
        } else {
            unset($data['pin']);
        }

        $usuario->update($data);

        if ($esAdminSolicitado === true) {
            Rol::asignarTenantAdmin($usuario->id_usuario, $idTenant, $request->user()->id_usuario);
        } elseif ($esAdminSolicitado === false) {
            Rol::revocarTenantAdmin($usuario->id_usuario, $idTenant);
            // Al quitarle el admin, cae al rol por defecto para no dejarlo
            // sin ningún permiso.
            Rol::asignarMiembro($usuario->id_usuario, $idTenant, $request->user()->id_usuario);
        }

        $respuesta = $usuario->toArray();
        unset($respuesta['es_superadmin']);
        $respuesta['es_admin'] = Rol::esAdminTenant($usuario->id_usuario, $idTenant);

        return response()->json($respuesta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;

        $membresia = Membresia::where('id_usuario', $id)->where('id_tenant', $idTenant)->firstOrFail();
        $usuario = Usuarios::findOrFail($id);

        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo'], 422);
        }

        $idsAdmin = Rol::idsAdminTenant($idTenant);

        if (in_array($usuario->id_usuario, $idsAdmin) && count($idsAdmin) <= 1) {
            return response()->json(['message' => 'No puedes eliminar al único administrador del equipo'], 422);
        }

        DB::table('usuario_rol')->where('id_usuario', $usuario->id_usuario)->where('id_tenant', $idTenant)->delete();
        $membresia->delete();

        // Si ese era su tenant activo, hay que reapuntarlo a otra empresa suya
        // (o, si esa era su única empresa, dar de baja la identidad completa).
        if ($usuario->id_tenant === $idTenant) {
            $otraMembresia = Membresia::where('id_usuario', $usuario->id_usuario)
                ->where('estado', 'activa')
                ->first();

            if ($otraMembresia) {
                $usuario->update(['id_tenant' => $otraMembresia->id_tenant]);
            } else {
                $usuario->delete();
            }
        }

        return response()->json(['message' => 'Usuario eliminado del equipo correctamente']);
    }
}
