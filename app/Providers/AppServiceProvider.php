<?php

namespace App\Providers;

use App\Auth\StratoPassportClient;
use App\Auth\TenantAwareAccessToken;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use SocialiteProviders\Auth0\Provider as Auth0Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

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

        // No hay vistas Blade: el link de "restablecer contraseña" del email
        // debe apuntar a una ruta de la SPA Angular, no a una route() de Laravel.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $email = urlencode($notifiable->getEmailForPasswordReset());
            return config('app.frontend_url')."/auth/reset-password?token={$token}&email={$email}";
        });

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('auth0', Auth0Provider::class);
        });
    }
}
