<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * $claves acepta varias claves separadas por comas en la definición de ruta
     * (ej. "permiso:erp_ventas.ver,erp_habitaciones.mantenimiento") para rutas
     * compartidas entre un permiso amplio existente y uno granular nuevo -- basta
     * con tener cualquiera de los dos (OR), no los dos a la vez. Laravel ya separa
     * cada segmento en un argumento variádico propio antes de llegar aquí.
     */
    public function handle(Request $request, Closure $next, string ...$claves): Response
    {
        $user = $request->user();
        $tieneAlguno = collect($claves)
            ->contains(fn (string $clave) => $user && Rol::tienePermiso($user->id_usuario, $user->id_tenant, $clave));

        if (! $tieneAlguno) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
        }

        return $next($request);
    }
}
