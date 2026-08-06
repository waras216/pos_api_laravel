<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirects de página completa del navegador (no llamadas JSON de la SPA),
// por eso viven en web.php y no en api.php: Socialite necesita la sesión
// del middleware "web" para validar el "state" de OAuth entre el redirect
// a Google y el callback de vuelta.
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
