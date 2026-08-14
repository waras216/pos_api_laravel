<?php

namespace App\Providers;

use App\Auth\StratoPassportClient;
use App\Auth\TenantAwareAccessToken;
use App\Models\Producto;
use App\Observers\ProductoObserver;
use App\Services\Erp\Pac\NullPacDriver;
use App\Services\Erp\Pac\PacDriverInterface;
use App\Services\Whatsapp\BaileysDriver;
use App\Services\Whatsapp\NullWhatsappDriver;
use App\Services\Whatsapp\WhatsappDriverInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Único punto donde se resuelve qué PAC timbra de verdad. Agregar un
        // proveedor nuevo (Facturama, SW, Finkok, ...) es: implementar su
        // driver contra PacDriverInterface y sumar su "case" aquí — el resto
        // del módulo de Facturación (FacturaService, controller, frontend)
        // no cambia.
        $this->app->bind(PacDriverInterface::class, function () {
            return match (config('pac.driver')) {
                // 'facturama' => new FacturamaDriver(config('pac.drivers.facturama')),
                default => new NullPacDriver(),
            };
        });

        // Mismo patrón para WhatsApp: agregar un driver nuevo es implementar
        // WhatsappDriverInterface y sumar su "case" aquí.
        $this->app->bind(WhatsappDriverInterface::class, function () {
            return match (config('whatsapp.driver')) {
                // 'meta' => new MetaCloudApiDriver(config('whatsapp.drivers.meta')),
                // 'twilio' => new TwilioWhatsappDriver(config('whatsapp.drivers.twilio')),
                'baileys' => new BaileysDriver(config('whatsapp.drivers.baileys')),
                default => new NullWhatsappDriver(),
            };
        });
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

        Producto::observe(ProductoObserver::class);
    }
}
