<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use App\Models\Usuarios;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Arranca el flujo de "Conectar Google Calendar" desde Integraciones.
     * A diferencia de redirect() (login), acá el usuario YA está
     * autenticado -- pero la navegación hacia Google es un redirect de
     * browser normal, que no lleva el Bearer token. Por eso el id_tenant
     * viaja cifrado en el propio parámetro "state" de OAuth en vez de
     * depender de sesión/cookies, y callback() lo recupera ahí.
     */
    public function conectarCalendario(Request $request)
    {
        $state = Crypt::encryptString(json_encode([
            'proposito' => 'calendar_connect',
            'id_tenant' => $request->user()->id_tenant,
        ]));

        $url = Socialite::driver('google')
            ->stateless()
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent', 'state' => $state])
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function callback(Request $request)
    {
        if ($datos = $this->decodificarEstadoCalendario($request->query('state'))) {
            return $this->callbackCalendario($datos);
        }

        $googleUser = Socialite::driver('google')->user();

        $email = $googleUser->getEmail();
        if (!$email) {
            return $this->redirectToFrontend(null, 'No pudimos obtener tu correo desde el proveedor social.');
        }

        $user = Usuarios::where('email', $email)->first();

        if (!$user) {
            $nombre = $googleUser->getName() ?: $googleUser->getNickname() ?: explode('@', $email)[0];

            // Los usuarios que entran por Google/redes no eligen contraseña
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

    /** @return array{id_tenant:int}|null */
    private function decodificarEstadoCalendario(?string $state): ?array
    {
        if (! $state) {
            return null;
        }

        try {
            $datos = json_decode(Crypt::decryptString($state), true);
        } catch (\Throwable) {
            return null;
        }

        return ($datos['proposito'] ?? null) === 'calendar_connect' && ! empty($datos['id_tenant'])
            ? $datos
            : null;
    }

    private function callbackCalendario(array $datos)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $integracion = Integracion::where('id_tenant', $datos['id_tenant'])->where('tipo', 'calendario')->first();

        if (! $integracion) {
            return redirect(config('app.frontend_url') . '/crm/integraciones?error=integracion_no_encontrada');
        }

        $configPrevia = $integracion->configuracion ?? [];

        $integracion->update([
            'estado' => 'conectada',
            'configuracion' => [
                'cuenta' => $googleUser->getEmail(),
                'access_token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken ?? $configPrevia['refresh_token'] ?? null,
                'expira_en' => now()->addSeconds($googleUser->expiresIn ?? 3600)->toIso8601String(),
            ],
        ]);

        return redirect(config('app.frontend_url') . '/crm/integraciones?conectado=calendario');
    }
}
