<?php

namespace App\Services;

use App\Models\Integracion;
use App\Models\Membresia;
use App\Models\Pipeline;
use App\Models\Rol;
use App\Models\Tenant;
use App\Models\Usuarios;
use Illuminate\Support\Str;

class OnboardingService
{
    /**
     * Provisiona un tenant nuevo para un usuario que se registra por primera
     * vez: crea el Tenant, la membresía de owner/admin, los pipelines por
     * defecto y el catálogo inicial de integraciones. Usado tanto por el
     * registro con email/password (AuthController) como por el alta vía
     * proveedores sociales (Auth0Controller) para que ambos flujos de
     * onboarding se mantengan idénticos.
     */
    public function provisionarTenantYUsuario(string $nombre, string $email, string $passwordHash): Usuarios
    {
        $tenant = Tenant::create([
            'nombre_tenant' => $nombre . ' Company',
            'subdominio' => Str::slug($nombre) . '-' . Str::random(6),
            'estado' => 'activo',
            'onboarding_completado' => false,
            'id_plan' => 1,
        ]);

        $user = Usuarios::create([
            'id_tenant' => $tenant->id_tenant,
            'nombre' => $nombre,
            'email' => $email,
            'password' => $passwordHash,
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

        return $user;
    }
}
