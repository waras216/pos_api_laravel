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
use Symfony\Component\Routing\RouterInterface;

 Route::post('/login',[AuthController::class, 'login']);
 Route::post('/register',[AuthController::class, 'register']);

 Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function(Request $request){
      return $request->user();
    });

    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('productos', ProductoController::class);

    //CRM
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('contactos', ContactoController::class);
    Route::apiResource('leads', LeadController::class);
    Route::apiResource('pipelines', PipelineController::class);
    Route::apiResource('oportunidades', OportunidadController::class);
    Route::apiResource('actividades', ActividadController::class);

    Route::get('notificaciones', [NotificacionController::class, 'index']);
    Route::patch('notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::patch('notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);
 });