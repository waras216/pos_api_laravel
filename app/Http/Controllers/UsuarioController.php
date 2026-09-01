<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\Rol;
use App\Models\Tenant;
use App\Models\Usuarios;
use App\Notifications\InvitacionEquipoNotification;
use App\Services\IntegracionService;
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
                'usuarios.google2fa_secret',
                'membresias.es_owner',
            ])
            ->map(function ($u) use ($idsAdmin, $rolesPorUsuario) {
                $data = $u->toArray();
                $data['es_admin'] = in_array($u->id_usuario, $idsAdmin);
                $data['roles'] = ($rolesPorUsuario[$u->id_usuario] ?? collect())->pluck('nombre')->values()->all();
                $data['tiene_2fa'] = ! is_null($u->google2fa_secret);
                unset($data['google2fa_secret']);
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
            // darse de alta solo con nombre, sin correo (entra por
            // /2fa-login, configurando su 2FA en un segundo paso vía
            // iniciarDosFa()/confirmarDosFa() -- no puede pasarse en este
            // mismo POST porque requiere escanear un QR). Si sí se da
            // correo, la contraseña es obligatoria.
            'email' => 'nullable|email',
            'password' => 'nullable|min:6',
            'telefono' => 'nullable|string|max:20',
            'es_admin' => 'sometimes|boolean',
            'id_rol' => ['nullable', 'integer', Rule::exists('roles', 'id_rol')->where('id_tenant', $idTenant)],
        ]);

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
                'telefono' => $data['telefono'] ?? null,
                // Sin correo no hay password real que usar -- se guarda un
                // hash de algo aleatorio que nadie puede escribir, así el
                // login por 2FA queda como única puerta de entrada.
                'password' => Hash::make($data['password'] ?? Str::random(40)),
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

        if (! empty($data['email']) && IntegracionService::conectada($idTenant, 'email')) {
            $tenant = Tenant::where('id_tenant', $idTenant)->first();
            $usuario->notify(new InvitacionEquipoNotification($tenant, $request->user()->nombre, $cuentaExistente));
        }

        $respuesta = $usuario->toArray();
        unset($respuesta['es_superadmin']);
        $respuesta['es_admin'] = $esAdmin;
        $respuesta['cuenta_existente'] = $cuentaExistente;
        $respuesta['tiene_2fa'] = ! is_null($usuario->google2fa_secret);

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
            'telefono' => 'nullable|string|max:20',
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
        $respuesta['tiene_2fa'] = ! is_null($usuario->google2fa_secret);

        return response()->json($respuesta);
    }

    /**
     * Genera un secreto TOTP nuevo para este usuario y lo devuelve como QR
     * (junto con la clave manual) para que lo escanee con una app tipo
     * Google Authenticator. No se persiste todavía -- viaja de ida y vuelta
     * con el frontend hasta confirmarDosFa(), para no dejar en la base
     * secretos de un enrolamiento a medio terminar.
     */
    public function iniciarDosFa(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        Membresia::where('id_usuario', $id)->where('id_tenant', $idTenant)->firstOrFail();
        $usuario = Usuarios::findOrFail($id);

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $google2fa->generateSecretKey();
        $otpauthUrl = $google2fa->getQRCodeUrl('STRATO Hub', $usuario->nombre, $secret);

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd(),
        );
        $qrSvg = (new \BaconQrCode\Writer($renderer))->writeString($otpauthUrl);

        return response()->json([
            'secret' => $secret,
            'qr' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg),
        ]);
    }

    /**
     * Confirma el enrolamiento de 2FA iniciado en iniciarDosFa(): valida un
     * código generado con el secreto recibido y, solo si es correcto, recién
     * ahí lo persiste (cifrado por el cast 'encrypted' del modelo).
     */
    public function confirmarDosFa(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        Membresia::where('id_usuario', $id)->where('id_tenant', $idTenant)->firstOrFail();
        $usuario = Usuarios::findOrFail($id);

        $data = $request->validate([
            'secret' => 'required|string',
            'codigo' => 'required|digits:6',
        ]);

        if (! (new \PragmaRX\Google2FA\Google2FA())->verifyKey($data['secret'], $data['codigo'])) {
            return response()->json(['message' => 'Código incorrecto'], 400);
        }

        $usuario->update(['google2fa_secret' => $data['secret']]);

        return response()->json(['tiene_2fa' => true]);
    }

    /**
     * Borra el 2FA configurado de este usuario (p. ej. perdió el teléfono),
     * para que un admin pueda volver a enrolarlo desde cero.
     */
    public function restablecerDosFa(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        Membresia::where('id_usuario', $id)->where('id_tenant', $idTenant)->firstOrFail();
        $usuario = Usuarios::findOrFail($id);

        $usuario->update(['google2fa_secret' => null]);

        return response()->json(['tiene_2fa' => false]);
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
