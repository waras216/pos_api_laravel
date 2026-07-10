<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use App\Models\Membresia;
use App\Models\Pipeline;
use App\Models\Rol;
use App\Models\Tenant;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $tenant = Tenant::create([
            'nombre_tenant' => $data['nombre'] . ' Company',
            'subdominio' => Str::slug($data['nombre']) . '-' . Str::random(6),
            'estado' => 'activo',
            'onboarding_completado' => false,
            'id_plan' => 1,
        ]);

        $user = Usuarios::create([
            'id_tenant' => $tenant->id_tenant,
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Rol::asignarTenantAdmin($user->id_usuario, $tenant->id_tenant);

        Membresia::create([
            'id_usuario' => $user->id_usuario,
            'id_tenant' => $tenant->id_tenant,
            'estado' => 'activa',
            'es_owner' => true,
            'unido_en' => now(),
        ]);

        Pipeline::create(['id_tenant' => $tenant->id_tenant, 'nombre' => 'Ventas', 'activo' => true]);
        Pipeline::create(['id_tenant' => $tenant->id_tenant, 'nombre' => 'Servicios', 'activo' => true]);

        foreach ([
            ['nombre' => 'WhatsApp Business', 'tipo' => 'whatsapp'],
            ['nombre' => 'Email Marketing', 'tipo' => 'email'],
            ['nombre' => 'Google Calendar', 'tipo' => 'calendario'],
            ['nombre' => 'Almacenamiento en la nube', 'tipo' => 'almacenamiento'],
        ] as $integracion) {
            Integracion::create([
                'id_tenant' => $tenant->id_tenant,
                'nombre' => $integracion['nombre'],
                'tipo' => $integracion['tipo'],
                'estado' => 'desconectada',
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $this->serializeUser($user),
            'token' => $token,
        ],201);
    }

    public function me(Request $request)
    {
        return response()->json($this->serializeUser($request->user()));
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

        return [
            'id_usuario' => $user->id_usuario,
            'id_tenant' => $user->id_tenant,
            'nombre' => $user->nombre,
            'email' => $user->email,
            'empresa' => $tenant?->nombre_tenant,
            'onboardingCompleto' => (bool) $tenant?->onboarding_completado,
            'nichoData' => $nichoData,
            'es_admin' => $tenant ? Rol::esAdminTenant($user->id_usuario, $tenant->id_tenant) : false,
            'es_superadmin' => Rol::esSuperAdmin($user->id_usuario),
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
