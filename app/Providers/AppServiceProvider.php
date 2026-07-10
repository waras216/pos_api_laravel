<?php

namespace App\Providers;

use App\Auth\StratoPassportClient;
use App\Auth\TenantAwareAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Password grant es solo para el backend/pruebas mientras el frontend
        // sigue en Sanctum. El cliente real de la SPA usará Authorization Code + PKCE.
        Passport::enablePasswordGrant();
        Passport::useAccessTokenEntity(TenantAwareAccessToken::class);
        Passport::useClientModel(StratoPassportClient::class);

        Passport::tokensExpireIn(now()->addMinutes(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
