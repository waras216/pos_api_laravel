<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Fase 3: Authorization Code + PKCE real (ver §06 del documento de
 * arquitectura). Estos endpoints envuelven el /oauth/token nativo de
 * Passport para que el refresh token nunca toque el JSON que ve el
 * navegador -- viaja únicamente como cookie httpOnly.
 */
class PassportAuthController extends Controller
{
    private const COOKIE = 'strato_refresh_token';

    /**
     * Establece la sesión web (cookie de sesión) que /oauth/authorize
     * necesita para reconocer al usuario sin mostrar una pantalla de login
     * aparte -- la pantalla de login sigue siendo la de la SPA.
     */
    public function loginWeb(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Usuarios::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        Auth::guard('web')->login($user);
        $request->session()->save();

        return response()->json(['message' => 'ok']);
    }

    /**
     * Intercambia un código de autorización (o un refresh token) por
     * tokens, delegando el trabajo real a Passport. El refresh_token sale
     * como cookie httpOnly; el body solo lleva el access_token de vida
     * corta.
     */
    public function token(Request $request)
    {
        $body = $this->exchangeConPassport($request->all());

        if (! isset($body['refresh_token'])) {
            return response()->json($body, $body['status'] ?? 400);
        }

        return $this->respuestaConCookie($body);
    }

    /**
     * Renueva el access_token usando el refresh_token que viaja en la
     * cookie httpOnly (nunca en el body ni accesible desde JS).
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie(self::COOKIE);

        if (! $refreshToken) {
            return response()->json(['message' => 'No hay sesión que renovar'], 401);
        }

        $body = $this->exchangeConPassport([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $request->input('client_id'),
            'scope' => '',
        ]);

        if (! isset($body['refresh_token'])) {
            return response()->json($body, $body['status'] ?? 401)
                ->withCookie(cookie()->forget(self::COOKIE));
        }

        return $this->respuestaConCookie($body);
    }

    public function logout(Request $request)
    {
        // El refresh token de la cookie no se revoca de forma explícita acá
        // (requeriría desencriptarlo para encontrar su fila) -- queda
        // documentado como pendiente. Sí revocamos el access_token vigente,
        // que es lo que de verdad habilita las rutas de negocio.
        $request->user()?->currentAccessToken()?->revoke();

        return response()->json(['message' => 'Sesión cerrada'])
            ->withCookie(cookie()->forget(self::COOKIE));
    }

    private function exchangeConPassport(array $params): array
    {
        $subRequest = Request::create('/oauth/token', 'POST', $params);
        $subRequest->headers->set('Accept', 'application/json');

        $response = app(Kernel::class)->handle($subRequest);
        $body = json_decode($response->getContent(), true) ?? [];
        $body['status'] = $response->getStatusCode();

        return $body;
    }

    private function respuestaConCookie(array $body)
    {
        $refreshToken = $body['refresh_token'];
        unset($body['refresh_token'], $body['status']);

        return response()->json($body)->cookie(
            self::COOKIE,
            $refreshToken,
            60 * 24 * 30,       // 30 días, igual que Passport::refreshTokensExpireIn()
            '/',
            null,
            ! app()->environment('local'), // Secure solo fuera de dev (dev es http, no https)
            true,                // httpOnly
            false,
            'Lax'
        );
    }
}
