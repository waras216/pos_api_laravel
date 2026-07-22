<?php

use App\Http\Controllers\Auth0Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirects de página completa del navegador (no llamadas JSON de la SPA),
// por eso viven en web.php y no en api.php: Socialite necesita la sesión
// del middleware "web" para validar el "state" de OAuth entre el redirect
// a Auth0 y el callback de vuelta.
Route::get('/auth/auth0/redirect', [Auth0Controller::class, 'redirect']);
Route::get('/auth/auth0/callback', [Auth0Controller::class, 'callback']);
