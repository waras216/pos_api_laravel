<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ExpirarSesionInactiva
{
    /**
     * Corre ANTES de auth:sanctum a propósito: Sanctum actualiza
     * last_used_at del token apenas se resuelve $request->user() dentro de
     * su propio middleware (Guard::updateLastUsedAt()), así que si este
     * chequeo corriera después, last_used_at siempre mostraría "ahora" y
     * nunca detectaría inactividad. Acá se lee el token crudo, sin pasar
     * por el guard, para ver su last_used_at tal cual quedó del request
     * anterior.
     *
     * El orden en Route::middleware(['sesion.inactiva', 'auth:sanctum'])
     * NO alcanza por sí solo -- Laravel reordena por su lista interna de
     * prioridad ($middlewarePriority en Kernel.php), que ya trae el
     * contrato AuthenticatesRequests (implementado por Authenticate) más
     * arriba que cualquier middleware de ruta no reconocido por el
     * framework. Por eso bootstrap/app.php también registra este
     * middleware en prependToPriorityList(before: AuthenticatesRequests::class)
     * -- usar la clase concreta Authenticate::class ahí NO funciona, porque
     * el array base de prioridad de Laravel referencia el contrato, no la
     * clase concreta, y la comparación es por string literal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();
        if (! $bearerToken) {
            return $next($request);
        }

        $accessToken = PersonalAccessToken::findToken($bearerToken);
        $minutos = (int) config('sanctum.inactivity_timeout_minutes');

        if ($accessToken && $accessToken->last_used_at
            && $accessToken->last_used_at->lt(now()->subMinutes($minutos))) {
            $accessToken->delete();

            return response()->json(['message' => 'Tu sesión expiró por inactividad.'], 401);
        }

        return $next($request);
    }
}
