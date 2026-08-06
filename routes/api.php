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
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Erp\InventarioController;
use App\Http\Controllers\Erp\OrdenCompraController;
use App\Http\Controllers\Erp\ProveedorController;
use App\Http\Controllers\Erp\MovimientoController;
use App\Http\Controllers\Erp\PedidoController;
use App\Http\Controllers\Erp\MesaController;
use App\Http\Controllers\Erp\HabitacionController;
use App\Http\Controllers\Erp\RecetaController;
use App\Http\Controllers\Erp\EmpleadoController;
use App\Http\Controllers\Erp\OrdenProduccionController;
use App\Http\Controllers\Erp\EnvioController;
use App\Http\Controllers\Erp\ProyectoController;
use App\Http\Controllers\Erp\ProyectoTareaController;
use App\Http\Controllers\Erp\ProyectoHoraController;
use App\Http\Controllers\Erp\CrmResumenController;
use App\Http\Controllers\Erp\DashboardController as ErpDashboardController;
use App\Http\Controllers\Erp\ReporteController as ErpReporteController;
use App\Http\Controllers\Erp\CuentaContableController;
use App\Http\Controllers\Erp\AsientoController;
use App\Http\Controllers\Erp\EstadosFinancierosController;
use App\Http\Controllers\Erp\NominaController;
use Symfony\Component\Routing\RouterInterface;

// Gatea un apiResource completo (incluyendo index/show) por permiso granular
// "{recurso}.ver|crear|editar|eliminar". Usar solo para recursos sensibles
// (erp/finanzas, erp/rrhh) — el resto usa permisoResourceSinVer para no
// romper dropdowns de otras pantallas que dependen de leer este recurso.
Route::macro('permisoResource', function (string $uri, string $controller, string $recurso) {
    return Route::apiResource($uri, $controller)
        ->middlewareFor(['index', 'show'], "permiso:{$recurso}.ver")
        ->middlewareFor('store', "permiso:{$recurso}.crear")
        ->middlewareFor('update', "permiso:{$recurso}.editar")
        ->middlewareFor('destroy', "permiso:{$recurso}.eliminar");
});

// Igual que permisoResource pero sin gatear index/show (fase 1: solo se
// restringe crear/editar/eliminar; "ver" queda abierto a cualquier miembro
// del tenant hasta que exista un editor de roles con dependencias entre
// permisos, para no romper formularios que leen otros recursos).
Route::macro('permisoResourceSinVer', function (string $uri, string $controller, string $recurso) {
    return Route::apiResource($uri, $controller)
        ->middlewareFor('store', "permiso:{$recurso}.crear")
        ->middlewareFor('update', "permiso:{$recurso}.editar")
        ->middlewareFor('destroy', "permiso:{$recurso}.eliminar");
});

 Route::post('/login',[AuthController::class, 'login']);
 Route::get('/pin-login/usuarios', [AuthController::class, 'pinUsuarios']);
 Route::post('/pin-login', [AuthController::class, 'pinLogin'])->middleware('throttle:8,1');
 Route::post('/register',[AuthController::class, 'register']);
 Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
 Route::post('/reset-password', [AuthController::class, 'resetPassword']);

 // Webhook de Stripe: fuera de auth:sanctum a propósito (lo llama Stripe,
 // no un usuario logueado). La autenticidad se valida por firma dentro del
 // controller (Stripe-Signature + STRIPE_WEBHOOK_SECRET), no por token.
 Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

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

 // Revertido temporalmente a Sanctum (2026-07-13): el cutover a Passport
 // (auth:api-oauth) de la Fase 3 dejaba 401 a todas las rutas de negocio
 // porque el frontend sigue logueando via AuthController::login, que emite
 // un token Sanctum, no Passport. Las rutas /auth/web-session, /auth/token,
 // /auth/refresh y /oauth/whoami de arriba quedan intactas para retomar
 // la migración cuando el frontend implemente el flujo OAuth2 PKCE.
 Route::middleware(['sesion.inactiva', 'auth:sanctum'])->group(function(){
    Route::get('/user', [AuthController::class, 'me']);
    Route::put('perfil', [AuthController::class, 'actualizarPerfil']);
    Route::patch('perfil/estado', [AuthController::class, 'actualizarEstado']);
    Route::post('perfil/foto', [AuthController::class, 'actualizarFoto']);
    Route::delete('perfil/foto', [AuthController::class, 'eliminarFoto']);

    Route::get('tenant', [TenantController::class, 'show']);
    Route::post('tenant/onboarding', [TenantController::class, 'completeOnboarding']);

    // Empresas a las que pertenece el usuario autenticado
    Route::get('mis-empresas', [MembresiaController::class, 'index']);
    Route::post('mis-empresas/{idTenant}/activar', [MembresiaController::class, 'activar']);

    // Todo lo de negocio requiere que el tenant activo del usuario no esté
    // suspendido. /user, tenant/* y mis-empresas/* quedan fuera para que un
    // usuario de una empresa suspendida pueda ver su sesión y cambiarse a
    // otra empresa suya que sí esté activa.
    Route::middleware(['tenant.activo', 'usuario.activo'])->group(function () {
        // Equipo (usuarios del tenant)
        Route::get('usuarios', [UsuarioController::class, 'index']);
        Route::middleware('admin.tenant')->group(function () {
            Route::post('usuarios', [UsuarioController::class, 'store']);
            Route::put('usuarios/{id}', [UsuarioController::class, 'update']);
            Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']);
            Route::put('tenant', [TenantController::class, 'update']);
            Route::post('tenant/logo', [TenantController::class, 'subirLogo']);
            Route::delete('tenant/logo', [TenantController::class, 'eliminarLogo']);

            // Roles y permisos granulares
            Route::get('permisos', [PermisoController::class, 'index']);
            Route::apiResource('roles', RolController::class)->except(['show']);
            Route::post('roles/{id}/usuarios/{idUsuario}', [RolController::class, 'asignarUsuario']);
            Route::delete('roles/{id}/usuarios/{idUsuario}', [RolController::class, 'quitarUsuario']);

            // Suscripción del tenant activo (Stripe Checkout + Billing Portal)
            Route::get('suscripcion', [SuscripcionController::class, 'show']);
            Route::get('suscripcion/planes', [SuscripcionController::class, 'planes']);
            Route::post('suscripcion/checkout', [SuscripcionController::class, 'checkout']);
            Route::post('suscripcion/portal', [SuscripcionController::class, 'portal']);
        });

        // Planes y empresas registradas (super-admin)
        Route::middleware('superadmin')->group(function () {
            Route::apiResource('planes', PlanController::class)->except(['show']);
            Route::get('empresas', [EmpresaController::class, 'index']);
            Route::get('empresas/kpis/nicho', [EmpresaController::class, 'kpisPorNicho']);
            Route::get('empresas/{id}', [EmpresaController::class, 'show']);
            Route::patch('empresas/{id}', [EmpresaController::class, 'update']);
            Route::delete('empresas/{id}', [EmpresaController::class, 'destroy']);
        });

        Route::permisoResourceSinVer('categorias', CategoriaController::class, 'categorias');
        Route::permisoResourceSinVer('productos', ProductoController::class, 'productos');

        //CRM
        Route::permisoResourceSinVer('clientes', ClienteController::class, 'clientes');
        Route::permisoResourceSinVer('contactos', ContactoController::class, 'contactos');
        Route::permisoResourceSinVer('leads', LeadController::class, 'leads');
        Route::post('leads/{id}/convertir', [LeadController::class, 'convertir'])->middleware('permiso:leads.editar');
        Route::permisoResourceSinVer('pipelines', PipelineController::class, 'pipelines');
        Route::permisoResourceSinVer('oportunidades', OportunidadController::class, 'oportunidades');
        Route::patch('oportunidades/{id}/etapa', [OportunidadController::class, 'moverEtapa'])->middleware('permiso:oportunidades.editar');
        Route::permisoResourceSinVer('actividades', ActividadController::class, 'actividades');

        Route::get('notificaciones', [NotificacionController::class, 'index']);
        Route::patch('notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
        Route::patch('notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);

        // Marketing
        Route::get('marketing/campanas', [CampanaController::class, 'index'])->middleware('permiso:marketing.ver');
        Route::post('marketing/campanas', [CampanaController::class, 'store'])->middleware('permiso:marketing.crear');
        Route::put('marketing/campanas/{id}', [CampanaController::class, 'update'])->middleware('permiso:marketing.editar');
        Route::delete('marketing/campanas/{id}', [CampanaController::class, 'destroy'])->middleware('permiso:marketing.eliminar');

        // Automatizaciones
        Route::apiResource('automatizaciones', AutomatizacionController::class)->except(['show'])
            ->middlewareFor('index', 'permiso:automatizaciones.ver')
            ->middlewareFor('store', 'permiso:automatizaciones.crear')
            ->middlewareFor('update', 'permiso:automatizaciones.editar')
            ->middlewareFor('destroy', 'permiso:automatizaciones.eliminar');
        Route::patch('automatizaciones/{id}/toggle', [AutomatizacionController::class, 'toggle'])->middleware('permiso:automatizaciones.editar');

        // Integraciones
        Route::get('integraciones', [IntegracionController::class, 'index'])->middleware('permiso:integraciones.ver');
        Route::patch('integraciones/{id}/toggle', [IntegracionController::class, 'toggle'])->middleware('permiso:integraciones.editar');

        // ERP
        Route::prefix('erp')->middleware('modulo:erp')->group(function () {
            Route::get('inventario/papelera', [InventarioController::class, 'papelera'])->middleware('permiso:erp_inventario.eliminar');
            Route::patch('inventario/{id}/restaurar', [InventarioController::class, 'restaurar'])->middleware('permiso:erp_inventario.eliminar');
            Route::permisoResourceSinVer('inventario', InventarioController::class, 'erp_inventario');
            Route::post('inventario/{id}/ajuste', [InventarioController::class, 'ajustarStock'])->middleware('permiso:erp_inventario.editar');
            Route::get('inventario/{id}/movimientos', [InventarioController::class, 'movimientos']);

            Route::get('proveedores/papelera', [ProveedorController::class, 'papelera'])->middleware('permiso:erp_proveedores.eliminar');
            Route::patch('proveedores/{id}/restaurar', [ProveedorController::class, 'restaurar'])->middleware('permiso:erp_proveedores.eliminar');
            Route::permisoResourceSinVer('proveedores', ProveedorController::class, 'erp_proveedores');

            Route::apiResource('compras', OrdenCompraController::class)->except(['update'])
                ->middlewareFor('store', 'permiso:erp_compras.crear')
                ->middlewareFor('destroy', 'permiso:erp_compras.eliminar');
            Route::patch('compras/{id}/recibir', [OrdenCompraController::class, 'recibir'])->middleware('permiso:erp_compras.editar');
            Route::patch('compras/{id}/cancelar', [OrdenCompraController::class, 'cancelar'])->middleware('permiso:erp_compras.editar');

            // erp/finanzas y erp/rrhh son sensibles: se gatea "ver" también,
            // no solo crear/editar/eliminar (ver plan SPRINT-21).
            Route::get('finanzas/papelera', [MovimientoController::class, 'papelera'])->middleware('permiso:erp_finanzas.eliminar');
            Route::patch('finanzas/{id}/restaurar', [MovimientoController::class, 'restaurar'])->middleware('permiso:erp_finanzas.eliminar');
            Route::apiResource('finanzas', MovimientoController::class)->except(['update'])
                ->middlewareFor(['index', 'show'], 'permiso:erp_finanzas.ver')
                ->middlewareFor('store', 'permiso:erp_finanzas.crear')
                ->middlewareFor('destroy', 'permiso:erp_finanzas.eliminar');
            Route::permisoResourceSinVer('ventas', PedidoController::class, 'erp_ventas');
            Route::patch('ventas/{id}/cancelar', [PedidoController::class, 'cancelar'])->middleware('permiso:erp_ventas.editar');

            // Contabilidad formal: plan de cuentas, asientos de partida doble
            // y estados financieros. Reutiliza el grupo de permisos
            // erp_finanzas.* que ya protegía el libro de caja plano.
            Route::get('contabilidad/cuentas', [CuentaContableController::class, 'index'])->middleware('permiso:erp_finanzas.ver');
            Route::post('contabilidad/cuentas', [CuentaContableController::class, 'store'])->middleware('permiso:erp_finanzas.crear');
            Route::put('contabilidad/cuentas/{id}', [CuentaContableController::class, 'update'])->middleware('permiso:erp_finanzas.editar');
            Route::delete('contabilidad/cuentas/{id}', [CuentaContableController::class, 'destroy'])->middleware('permiso:erp_finanzas.eliminar');

            Route::get('contabilidad/asientos', [AsientoController::class, 'index'])->middleware('permiso:erp_finanzas.ver');
            Route::post('contabilidad/asientos', [AsientoController::class, 'store'])->middleware('permiso:erp_finanzas.crear');
            Route::get('contabilidad/asientos/{id}', [AsientoController::class, 'show'])->middleware('permiso:erp_finanzas.ver');
            Route::post('contabilidad/asientos/{id}/reversar', [AsientoController::class, 'reversar'])->middleware('permiso:erp_finanzas.crear');

            Route::get('contabilidad/balance-comprobacion', [EstadosFinancierosController::class, 'balanceComprobacion'])->middleware('permiso:erp_finanzas.ver');
            Route::get('contabilidad/estado-resultados', [EstadosFinancierosController::class, 'estadoResultados'])->middleware('permiso:erp_finanzas.ver');
            Route::get('contabilidad/balance-general', [EstadosFinancierosController::class, 'balanceGeneral'])->middleware('permiso:erp_finanzas.ver');

            // Mesas/comandas del terminal POS de restaurante (SPRINT-39).
            Route::get('mesas', [MesaController::class, 'index']);
            Route::post('mesas', [MesaController::class, 'store'])->middleware('permiso:erp_ventas.editar');
            Route::delete('mesas/{id}', [MesaController::class, 'destroy'])->middleware('permiso:erp_ventas.eliminar');
            Route::patch('mesas/{id}/abrir', [MesaController::class, 'abrir'])->middleware('permiso:erp_ventas.crear');
            Route::patch('mesas/{id}/pedir-cuenta', [MesaController::class, 'pedirCuenta'])->middleware('permiso:erp_ventas.editar');
            Route::post('mesas/{id}/items', [MesaController::class, 'agregarItem'])->middleware('permiso:erp_ventas.crear');
            Route::patch('mesas/{id}/items/{itemId}', [MesaController::class, 'actualizarItem'])->middleware('permiso:erp_ventas.editar');
            Route::delete('mesas/{id}/items/{itemId}', [MesaController::class, 'quitarItem'])->middleware('permiso:erp_ventas.editar');
            Route::post('mesas/{id}/enviar-cocina', [MesaController::class, 'enviarCocina'])->middleware('permiso:erp_ventas.editar');
            Route::post('mesas/{id}/cobrar', [MesaController::class, 'cobrar'])->middleware('permiso:erp_ventas.crear');

            // Habitaciones/room-service del terminal POS de hotel (SPRINT-39).
            Route::get('habitaciones', [HabitacionController::class, 'index']);
            Route::post('habitaciones', [HabitacionController::class, 'store'])->middleware('permiso:erp_ventas.editar');
            Route::delete('habitaciones/{id}', [HabitacionController::class, 'destroy'])->middleware('permiso:erp_ventas.eliminar');
            Route::patch('habitaciones/{id}/check-in', [HabitacionController::class, 'checkIn'])->middleware('permiso:erp_ventas.crear');
            Route::post('habitaciones/{id}/consumos', [HabitacionController::class, 'agregarConsumo'])->middleware('permiso:erp_ventas.crear');
            Route::delete('habitaciones/{id}/consumos/{consumoId}', [HabitacionController::class, 'quitarConsumo'])->middleware('permiso:erp_ventas.editar');
            Route::patch('habitaciones/{id}/mantenimiento', [HabitacionController::class, 'mantenimiento'])->middleware('permiso:erp_ventas.editar');
            Route::post('habitaciones/{id}/check-out', [HabitacionController::class, 'checkOut'])->middleware('permiso:erp_ventas.crear');

            // Recetas del terminal POS de farmacia (SPRINT-39).
            Route::get('recetas', [RecetaController::class, 'index']);
            Route::post('recetas', [RecetaController::class, 'store'])->middleware('permiso:erp_ventas.crear');
            Route::post('recetas/dispensar-lote', [RecetaController::class, 'dispensarLote'])->middleware('permiso:erp_ventas.crear');
            // Debe ir antes de permisoResource('rrhh', ...): su ruta 'show'
            // (rrhh/{rrhh}) intercepta cualquier ruta literal registrada después.
            Route::get('rrhh/nomina', [NominaController::class, 'index'])->middleware('permiso:erp_rrhh.ver');
            Route::post('rrhh/nomina/procesar', [NominaController::class, 'procesar'])->middleware('permiso:erp_rrhh.crear');
            Route::permisoResource('rrhh', EmpleadoController::class, 'erp_rrhh');
            Route::permisoResourceSinVer('fabricacion', OrdenProduccionController::class, 'erp_fabricacion');
            Route::get('scm/papelera', [EnvioController::class, 'papelera'])->middleware('permiso:erp_scm.eliminar');
            Route::patch('scm/{id}/restaurar', [EnvioController::class, 'restaurar'])->middleware('permiso:erp_scm.eliminar');
            Route::permisoResourceSinVer('scm', EnvioController::class, 'erp_scm');
            Route::permisoResourceSinVer('proyectos', ProyectoController::class, 'erp_proyectos');

            Route::get('proyectos/{idProyecto}/tareas', [ProyectoTareaController::class, 'index']);
            Route::post('proyectos/{idProyecto}/tareas', [ProyectoTareaController::class, 'store'])->middleware('permiso:erp_proyectos.editar');
            Route::put('proyectos/{idProyecto}/tareas/{id}', [ProyectoTareaController::class, 'update'])->middleware('permiso:erp_proyectos.editar');
            Route::delete('proyectos/{idProyecto}/tareas/{id}', [ProyectoTareaController::class, 'destroy'])->middleware('permiso:erp_proyectos.editar');

            Route::get('proyectos/{idProyecto}/horas', [ProyectoHoraController::class, 'index']);
            Route::post('proyectos/{idProyecto}/horas', [ProyectoHoraController::class, 'store'])->middleware('permiso:erp_proyectos.editar');
            Route::delete('proyectos/{idProyecto}/horas/{id}', [ProyectoHoraController::class, 'destroy'])->middleware('permiso:erp_proyectos.editar');

            Route::get('crm/resumen', [CrmResumenController::class, 'resumen']);
            Route::get('crm/interacciones', [CrmResumenController::class, 'interacciones']);

            Route::get('dashboard/resumen', [ErpDashboardController::class, 'resumen']);
            Route::get('reportes/resumen', [ErpReporteController::class, 'resumen']);
        });
    });
 });
