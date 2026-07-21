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
        $middleware->alias([
            'admin.tenant' => \App\Http\Middleware\EnsureTenantAdmin::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'tenant.activo' => \App\Http\Middleware\EnsureTenantActive::class,
            'usuario.activo' => \App\Http\Middleware\EnsureUserActive::class,
            'permiso' => \App\Http\Middleware\CheckPermiso::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
