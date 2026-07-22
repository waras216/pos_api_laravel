<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Services\OnboardingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class Auth0Controller extends Controller
{
    public function redirect()
    {
        // Sin este parámetro, Auth0 muestra su propia pantalla de Universal
        // Login (con opción de email/password) antes de llegar a Google.
        return Socialite::driver('auth0')->with(['connection' => 'google-oauth2'])->redirect();
    }

    public function callback()
    {
        $auth0User = Socialite::driver('auth0')->user();

        $email = $auth0User->getEmail();
        if (!$email) {
            return $this->redirectToFrontend(null, 'No pudimos obtener tu correo desde el proveedor social.');
        }

        $user = Usuarios::where('email', $email)->first();

        if (!$user) {
            $nombre = $auth0User->getName() ?: $auth0User->getNickname() ?: explode('@', $email)[0];

            // Los usuarios que entran por Auth0/redes no eligen contraseña
            // propia; se genera una aleatoria e inutilizable solo para
            // satisfacer la columna NOT NULL -- el login por password nunca
            // la va a usar.
            $user = app(OnboardingService::class)->provisionarTenantYUsuario(
                $nombre,
                $email,
                Hash::make(Str::random(40)),
            );
        }

        if ($user->tenant && $user->tenant->estado !== 'activo') {
            return $this->redirectToFrontend(null, 'Tu empresa fue suspendida. Contacta a soporte.');
        }

        if ($user->estado === 'suspendido') {
            return $this->redirectToFrontend(null, 'Tu cuenta fue suspendida. Contacta a un administrador.');
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->redirectToFrontend($token);
    }

    private function redirectToFrontend(?string $token, ?string $error = null)
    {
        $params = $token ? ['token' => $token] : ['error' => $error];

        return redirect(config('app.frontend_url') . '/auth/social-callback?' . http_build_query($params));
    }
}
