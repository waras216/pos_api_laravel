<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\OportunidadController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\CampanaController;
use App\Http\Controllers\AutomatizacionController;
use App\Http\Controllers\IntegracionController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\Erp\InventarioController;
use App\Http\Controllers\Erp\OrdenCompraController;
use App\Http\Controllers\Erp\ProveedorController;
use App\Http\Controllers\Erp\MovimientoController;
use App\Http\Controllers\Erp\PedidoController;
use App\Http\Controllers\Erp\EmpleadoController;
use App\Http\Controllers\Erp\OrdenProduccionController;
use App\Http\Controllers\Erp\EnvioController;
use App\Http\Controllers\Erp\ProyectoController;
use App\Http\Controllers\Erp\CrmResumenController;
use App\Http\Controllers\Erp\DashboardController as ErpDashboardController;
use Symfony\Component\Routing\RouterInterface;

 Route::post('/login',[AuthController::class, 'login']);
 Route::post('/register',[AuthController::class, 'register']);

 // Authorization Code + PKCE (Fase 3, ver §06). /oauth/authorize y la mitad
 // "cruda" de /oauth/token los registra Passport solo; estos son la capa
 // que necesita la SPA encima (sesión web para el redirect, y que el
 // refresh_token nunca llegue a JS).
 Route::post('/auth/web-session', [PassportAuthController::class, 'loginWeb']);
 Route::post('/auth/token', [PassportAuthController::class, 'token']);
 Route::post('/auth/refresh', [PassportAuthController::class, 'refresh']);
 Route::middleware('auth:api-oauth')->post('/auth/oauth-logout', [PassportAuthController::class, 'logout']);

 // Diagnóstico de la migración a OAuth2/Passport (Fase 3). No forma parte
 // del contrato de negocio: valida en paralelo que un token Passport
 // resuelve al mismo usuario/tenant que hoy resuelve auth:sanctum.
 Route::middleware('auth:api-oauth')->get('/oauth/whoami', function (Request $request) {
     $user = $request->user();
     return response()->json([
         'id_usuario' => $user->id_usuario,
         'id_tenant' => $user->id_tenant,
         'email' => $user->email,
         'token_scopes' => $user->currentAccessToken()?->scopes,
     ]);
 });

 // Cutover Fase 3: estas rutas de negocio ya validan contra Passport
 // (auth:api-oauth), no contra Sanctum. logout vive en /auth/oauth-logout
 // (necesita revoke(), no delete() -- son objetos de token distintos).
 Route::middleware('auth:api-oauth')->group(function(){
    Route::get('/user', [AuthController::class, 'me']);

    Route::get('tenant', [TenantController::class, 'show']);
    Route::post('tenant/onboarding', [TenantController::class, 'completeOnboarding']);

    // Empresas a las que pertenece el usuario autenticado
    Route::get('mis-empresas', [MembresiaController::class, 'index']);
    Route::post('mis-empresas/{idTenant}/activar', [MembresiaController::class, 'activar']);

    // Equipo (usuarios del tenant)
    Route::get('usuarios', [UsuarioController::class, 'index']);
    Route::middleware('admin.tenant')->group(function () {
        Route::post('usuarios', [UsuarioController::class, 'store']);
        Route::put('usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']);
    });

    // Planes (super-admin)
    Route::middleware('superadmin')->group(function () {
        Route::apiResource('planes', PlanController::class)->except(['show']);
    });

    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('productos', ProductoController::class);

    //CRM
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('contactos', ContactoController::class);
    Route::apiResource('leads', LeadController::class);
    Route::post('leads/{id}/convertir', [LeadController::class, 'convertir']);
    Route::apiResource('pipelines', PipelineController::class);
    Route::apiResource('oportunidades', OportunidadController::class);
    Route::patch('oportunidades/{id}/etapa', [OportunidadController::class, 'moverEtapa']);
    Route::apiResource('actividades', ActividadController::class);

    Route::get('notificaciones', [NotificacionController::class, 'index']);
    Route::patch('notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::patch('notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);

    // Marketing
    Route::get('marketing/campanas', [CampanaController::class, 'index']);
    Route::post('marketing/campanas', [CampanaController::class, 'store']);
    Route::put('marketing/campanas/{id}', [CampanaController::class, 'update']);
    Route::delete('marketing/campanas/{id}', [CampanaController::class, 'destroy']);

    // Automatizaciones
    Route::apiResource('automatizaciones', AutomatizacionController::class)->except(['show']);
    Route::patch('automatizaciones/{id}/toggle', [AutomatizacionController::class, 'toggle']);

    // Integraciones
    Route::get('integraciones', [IntegracionController::class, 'index']);
    Route::patch('integraciones/{id}/toggle', [IntegracionController::class, 'toggle']);

    // ERP
    Route::prefix('erp')->group(function () {
        Route::get('inventario/papelera', [InventarioController::class, 'papelera']);
        Route::patch('inventario/{id}/restaurar', [InventarioController::class, 'restaurar']);
        Route::apiResource('inventario', InventarioController::class);
        Route::post('inventario/{id}/ajuste', [InventarioController::class, 'ajustarStock']);
        Route::get('inventario/{id}/movimientos', [InventarioController::class, 'movimientos']);

        Route::get('proveedores/papelera', [ProveedorController::class, 'papelera']);
        Route::patch('proveedores/{id}/restaurar', [ProveedorController::class, 'restaurar']);
        Route::apiResource('proveedores', ProveedorController::class);

        Route::apiResource('compras', OrdenCompraController::class)->except(['update']);
        Route::patch('compras/{id}/recibir', [OrdenCompraController::class, 'recibir']);
        Route::patch('compras/{id}/cancelar', [OrdenCompraController::class, 'cancelar']);

        Route::get('finanzas/papelera', [MovimientoController::class, 'papelera']);
        Route::patch('finanzas/{id}/restaurar', [MovimientoController::class, 'restaurar']);
        Route::apiResource('finanzas', MovimientoController::class)->except(['update']);
        Route::apiResource('ventas', PedidoController::class);
        Route::patch('ventas/{id}/cancelar', [PedidoController::class, 'cancelar']);
        Route::apiResource('rrhh', EmpleadoController::class);
        Route::apiResource('fabricacion', OrdenProduccionController::class);
        Route::get('scm/papelera', [EnvioController::class, 'papelera']);
        Route::patch('scm/{id}/restaurar', [EnvioController::class, 'restaurar']);
        Route::apiResource('scm', EnvioController::class);
        Route::apiResource('proyectos', ProyectoController::class);

        Route::get('crm/resumen', [CrmResumenController::class, 'resumen']);
        Route::get('crm/interacciones', [CrmResumenController::class, 'interacciones']);

        Route::get('dashboard/resumen', [ErpDashboardController::class, 'resumen']);
    });
 });
