<?php

namespace App\Services;

use App\Models\Integracion;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Crea eventos reales en el Google Calendar del tenant, usando el token
 * OAuth guardado por GoogleAuthController::callback() cuando conecta la
 * integración "calendario" (ver IntegracionController). No usa el paquete
 * de Socialite para esta parte -- son llamadas REST directas a la Calendar
 * API, más simples de razonar que forzar el objeto User de Socialite a
 * hacer algo para lo que no está pensado.
 */
class GoogleCalendarService
{
    /**
     * Devuelve el ID del evento creado (para guardarlo en
     * actividades.id_evento_google y poder editarlo/borrarlo después), o
     * null si no hay integración conectada o la llamada falló.
     */
    public function crearEvento(int $idTenant, string $titulo, ?string $descripcion, \DateTimeInterface|string|null $fechaInicio, \DateTimeInterface|string|null $fechaFin): ?string
    {
        $accessToken = $this->accessTokenDelTenant($idTenant);
        if (! $accessToken) {
            return null;
        }

        $zona = $this->zonaHorariaDelTenant($idTenant);
        [$inicio, $fin] = $this->rango($fechaInicio, $fechaFin, $zona);

        $respuesta = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                'summary' => $titulo,
                'description' => $descripcion,
                'start' => ['dateTime' => $inicio->toRfc3339String(), 'timeZone' => $zona],
                'end' => ['dateTime' => $fin->toRfc3339String(), 'timeZone' => $zona],
            ]);

        if ($respuesta->failed()) {
            Log::warning('GoogleCalendarService: no se pudo crear el evento', [
                'id_tenant' => $idTenant,
                'status' => $respuesta->status(),
                'body' => $respuesta->body(),
            ]);

            return null;
        }

        return $respuesta->json('id');
    }

    public function actualizarEvento(int $idTenant, string $idEvento, string $titulo, ?string $descripcion, \DateTimeInterface|string|null $fechaInicio, \DateTimeInterface|string|null $fechaFin): void
    {
        $accessToken = $this->accessTokenDelTenant($idTenant);
        if (! $accessToken) {
            return;
        }

        $zona = $this->zonaHorariaDelTenant($idTenant);
        [$inicio, $fin] = $this->rango($fechaInicio, $fechaFin, $zona);

        $respuesta = Http::withToken($accessToken)
            ->patch("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$idEvento}", [
                'summary' => $titulo,
                'description' => $descripcion,
                'start' => ['dateTime' => $inicio->toRfc3339String(), 'timeZone' => $zona],
                'end' => ['dateTime' => $fin->toRfc3339String(), 'timeZone' => $zona],
            ]);

        if ($respuesta->failed()) {
            Log::warning('GoogleCalendarService: no se pudo actualizar el evento', [
                'id_tenant' => $idTenant,
                'id_evento' => $idEvento,
                'status' => $respuesta->status(),
                'body' => $respuesta->body(),
            ]);
        }
    }

    public function eliminarEvento(int $idTenant, string $idEvento): void
    {
        $accessToken = $this->accessTokenDelTenant($idTenant);
        if (! $accessToken) {
            return;
        }

        $respuesta = Http::withToken($accessToken)
            ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$idEvento}");

        // Google ya devuelve 410 Gone si el evento fue borrado a mano desde
        // el propio Calendar -- no es un error real, no hay nada que loguear.
        if ($respuesta->failed() && $respuesta->status() !== 410) {
            Log::warning('GoogleCalendarService: no se pudo borrar el evento', [
                'id_tenant' => $idTenant,
                'id_evento' => $idEvento,
                'status' => $respuesta->status(),
                'body' => $respuesta->body(),
            ]);
        }
    }

    private function rango(\DateTimeInterface|string|null $fechaInicio, \DateTimeInterface|string|null $fechaFin, string $zona): array
    {
        $inicio = $fechaInicio ? Carbon::parse($fechaInicio, $zona) : now($zona);
        $fin = $fechaFin ? Carbon::parse($fechaFin, $zona) : $inicio->copy()->addHour();

        return [$inicio, $fin];
    }

    /**
     * Sin esto, una fecha "en blanco" como "2026-08-19" se interpreta con
     * el timezone por defecto de la app (UTC, ver config/app.php) en vez
     * del real del tenant -- medianoche UTC del día 19 cae la tarde/noche
     * del 18 en zonas horarias negativas (ej. America/Mexico_City,
     * UTC-6), y el evento aparece "un día antes" en Google Calendar.
     * Confirmado con una prueba real.
     */
    private function zonaHorariaDelTenant(int $idTenant): string
    {
        return Tenant::find($idTenant)?->zona_horaria ?? config('app.timezone');
    }

    private function accessTokenDelTenant(int $idTenant): ?string
    {
        $integracion = Integracion::where('id_tenant', $idTenant)->where('tipo', 'calendario')->first();

        if (! $integracion || $integracion->estado !== 'conectada') {
            return null;
        }

        return $this->obtenerAccessTokenValido($integracion);
    }

    /**
     * Refresca el access_token con el refresh_token guardado si ya venció.
     * Devuelve null (sin lanzar) si no hay refresh_token o el refresh falla
     * -- quedarse sin sincronizar un evento no debe tumbar la operación que
     * lo disparó (ver AutomatizacionEngine::crearActividad).
     */
    private function obtenerAccessTokenValido(Integracion $integracion): ?string
    {
        $config = $integracion->configuracion ?? [];
        $expiraEn = isset($config['expira_en']) ? Carbon::parse($config['expira_en']) : null;

        if ($expiraEn && $expiraEn->isFuture()) {
            return $config['access_token'] ?? null;
        }

        $refreshToken = $config['refresh_token'] ?? null;
        if (! $refreshToken) {
            return $config['access_token'] ?? null;
        }

        $respuesta = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($respuesta->failed()) {
            Log::warning('GoogleCalendarService: no se pudo refrescar el token', ['id_tenant' => $integracion->id_tenant]);

            return null;
        }

        $data = $respuesta->json();

        $integracion->update([
            'configuracion' => array_merge($config, [
                'access_token' => $data['access_token'],
                'expira_en' => now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String(),
            ]),
        ]);

        return $data['access_token'];
    }
}
