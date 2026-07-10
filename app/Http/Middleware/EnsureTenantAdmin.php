<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! Rol::esAdminTenant($user->id_usuario, $user->id_tenant)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}
