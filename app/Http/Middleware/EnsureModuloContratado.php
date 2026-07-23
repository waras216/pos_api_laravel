<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuloContratado
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        $user = $request->user();
        $campo = 'modulo_' . $modulo;

        if (! $user || ! $user->tenant || ! $user->tenant->{$campo}) {
            return response()->json(['message' => 'Este módulo no está contratado para tu empresa.'], 403);
        }

        return $next($request);
    }
}
