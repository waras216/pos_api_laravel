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
use Symfony\Component\Routing\RouterInterface;

 Route::post('/login',[AuthController::class, 'login']);
 Route::post('/register',[AuthController::class, 'register']);

 Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    Route::get('tenant', [TenantController::class, 'show']);
    Route::post('tenant/onboarding', [TenantController::class, 'completeOnboarding']);

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
 });
