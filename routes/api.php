<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

 Route::post('/login',[AuthController::class, 'login']);
 Route::post('/register',[AuthController::class, 'register']);

 Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function(Request $request){
      return $request->user();
    });

    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('productos', ProductoController::class);
 });