<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);

        $middleware->alias([
            'admin.tenant' => \App\Http\Middleware\EnsureTenantAdmin::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'tenant.activo' => \App\Http\Middleware\EnsureTenantActive::class,
            'usuario.activo' => \App\Http\Middleware\EnsureUserActive::class,
            'permiso' => \App\Http\Middleware\CheckPermiso::class,
            'modulo' => \App\Http\Middleware\EnsureModuloContratado::class,
            'sesion.inactiva' => \App\Http\Middleware\ExpirarSesionInactiva::class,
        ]);

        // Sin esto, el orden real de ejecución NO respeta el orden del array
        // en Route::middleware([...]) para 'sesion.inactiva' vs 'auth:sanctum':
        // Laravel reordena la ejecución según su lista interna de prioridad
        // ($middlewarePriority), que ya trae Authenticate (auth:sanctum) más
        // arriba que cualquier middleware de ruta no reconocido por el
        // framework. Eso hacía que Sanctum tocara last_used_at a "ahora"
        // ANTES de que este middleware llegara a leerlo, dejando la
        // expiración por inactividad completamente inoperante.
        $middleware->prependToPriorityList(
            before: \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            prepend: \App\Http\Middleware\ExpirarSesionInactiva::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
