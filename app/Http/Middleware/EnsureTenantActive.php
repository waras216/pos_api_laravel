<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->tenant && $user->tenant->estado !== 'activo') {
            return response()->json(['message' => 'Tu empresa fue suspendida. Contacta a soporte.'], 401);
        }

        return $next($request);
    }
}
