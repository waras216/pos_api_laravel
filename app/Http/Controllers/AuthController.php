<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\Rol;
use App\Models\Usuarios;
use App\Services\OnboardingService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Usuarios::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'credenciales incorrectas'
            ],400);
        }

        if ($user->tenant && $user->tenant->estado !== 'activo') {
            return response()->json([
                'message' => 'Tu empresa fue suspendida. Contacta a soporte.'
            ], 403);
        }

        if ($user->estado === 'suspendido') {
            return response()->json([
                'message' => 'Tu cuenta fue suspendida. Contacta a un administrador.'
            ], 403);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $this->serializeUser($user),
            'token' => $token
        ]);
    }
    
    public function register(Request $request){
        $data = $request -> validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
        ]);

        $user = app(OnboardingService::class)->provisionarTenantYUsuario(
            $data['nombre'],
            $data['email'],
            Hash::make($data['password']),
        );

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $this->serializeUser($user),
            'token' => $token,
        ],201);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('usuarios')->sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'No pudimos enviar el enlace de recuperación',
            ], 400);
        }

        return response()->json([
            'message' => 'Te enviamos un enlace de recuperación a tu correo',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('usuarios')->reset(
            $data,
            function (Usuarios $user, string $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'El enlace de recuperación es inválido o expiró',
            ], 400);
        }

        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->serializeUser($request->user()));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'email' => 'required|email|max:200|unique:usuarios,email,' . $user->id_usuario . ',id_usuario',
        ]);

        $user->update($data);

        return response()->json($this->serializeUser($user));
    }

    /**
     * Autoservicio: el propio usuario cambia su estado de disponibilidad
     * (activo/ocupado). Suspender a alguien es una acción de admin, se hace
     * desde UsuarioController::update.
     */
    public function actualizarEstado(Request $request)
    {
        $data = $request->validate([
            'estado' => 'required|string|in:activo,ocupado',
        ]);

        $user = $request->user();
        $user->update($data);

        return response()->json($this->serializeUser($user));
    }

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        if ($user->foto_perfil) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        $path = $request->file('foto')->store('perfiles', 'public');
        if ($path === false) {
            return response()->json(['message' => 'No se pudo guardar la imagen.'], 500);
        }

        $user->foto_perfil = $path;
        $user->save();

        return response()->json($this->serializeUser($user));
    }

    public function eliminarFoto(Request $request)
    {
        $user = $request->user();

        if ($user->foto_perfil) {
            Storage::disk('public')->delete($user->foto_perfil);
            $user->foto_perfil = null;
            $user->save();
        }

        return response()->json($this->serializeUser($user));
    }

    public function serializeUser(Usuarios $user): array
    {
        $user->loadMissing('tenant.negocio', 'tenant.plan');
        $tenant = $user->tenant;

        $nichoData = null;
        if ($tenant && $tenant->onboarding_completado) {
            $nichoData = array_merge([
                'nicho' => optional($tenant->negocio)->slug,
                'moneda' => $tenant->moneda,
                'modulos' => [
                    'crm' => (bool) $tenant->modulo_crm,
                    'pos' => (bool) $tenant->modulo_pos,
                    'erp' => (bool) $tenant->modulo_erp,
                ],
            ], $tenant->datos_nicho ?? []);
        }

        $esAdmin = $tenant ? Rol::esAdminTenant($user->id_usuario, $tenant->id_tenant) : false;
        $esSuperadmin = Rol::esSuperAdmin($user->id_usuario);

        return [
            'id_usuario' => $user->id_usuario,
            'id_tenant' => $user->id_tenant,
            'nombre' => $user->nombre,
            'email' => $user->email,
            'estado' => $user->estado,
            'foto_perfil' => $user->foto_perfil ? Storage::disk('public')->url($user->foto_perfil) : null,
            'empresa' => $tenant?->nombre_tenant,
            'logo' => $tenant?->logo ? Storage::disk('public')->url($tenant->logo) : null,
            'onboardingCompleto' => (bool) $tenant?->onboarding_completado,
            'sector' => $tenant?->sector,
            'idioma' => $tenant?->idioma,
            'zonaHoraria' => $tenant?->zona_horaria,
            'nichoData' => $nichoData,
            'es_admin' => $esAdmin,
            'es_superadmin' => $esSuperadmin,
            // Claves de permiso granular ("recurso.accion") del usuario en el
            // tenant activo. ['*'] significa "todo permitido" (admin/superadmin).
            'permisos' => ($esAdmin || $esSuperadmin)
                ? ['*']
                : ($tenant ? Rol::permisosDe($user->id_usuario, $tenant->id_tenant) : []),
            'plan' => $tenant?->plan ? [
                'nombre_plan' => $tenant->plan->nombre_plan,
                'max_usuarios' => $tenant->plan->max_usuarios,
                'usuarios_actuales' => Membresia::where('id_tenant', $tenant->id_tenant)->where('estado', 'activa')->count(),
            ] : null,
            'membresias' => Membresia::where('membresias.id_usuario', $user->id_usuario)
                ->where('membresias.estado', 'activa')
                ->join('tenants', 'tenants.id_tenant', '=', 'membresias.id_tenant')
                ->orderBy('tenants.nombre_tenant')
                ->get([
                    'membresias.id_tenant',
                    'tenants.nombre_tenant as empresa',
                    'membresias.es_owner',
                ]),
        ];
    }
}
