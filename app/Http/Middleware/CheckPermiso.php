<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    public function handle(Request $request, Closure $next, string $clave): Response
    {
        $user = $request->user();

        if (! $user || ! Rol::tienePermiso($user->id_usuario, $user->id_tenant, $clave)) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
        }

        return $next($request);
    }
}
