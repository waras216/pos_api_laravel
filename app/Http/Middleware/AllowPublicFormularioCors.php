<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * config/cors.php restringe allowed_origins al frontend propio de STRATO a
 * propósito (la API usa Sanctum con cookies/credenciales, abrir CORS global
 * a "*" ahí sería un hueco real). El formulario público de
 * PublicFormularioController necesita exactamente lo opuesto: lo llama
 * fetch() desde la landing externa del tenant (WordPress u otra), dominio
 * que no podemos conocer de antemano.
 *
 * Laravel::HandleCors corta el preflight OPTIONS antes de que la ruta (y su
 * middleware) llegue a ejecutarse, así que registrar esto como middleware
 * de ruta no alcanzaría a interceptarlo a tiempo -- por eso se registra
 * PREPENDido de forma global en bootstrap/app.php, y acá adentro se
 * chequea la ruta a mano: para cualquier otro request, esto es un no-op
 * total y HandleCors sigue mandando exactamente igual que antes.
 */
class AllowPublicFormularioCors
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('api/public/formularios/*')) {
            return $next($request);
        }

        $response = $request->getMethod() === 'OPTIONS' ? response('', 204) : $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        return $response;
    }
}
