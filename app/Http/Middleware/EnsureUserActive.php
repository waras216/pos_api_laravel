<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->estado === 'suspendido') {
            return response()->json(['message' => 'Tu cuenta fue suspendida. Contacta a un administrador.'], 401);
        }

        return $next($request);
    }
}
