<?php

namespace App\Services;

use App\Models\Integracion;
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
    public function crearEvento(int $idTenant, string $titulo, ?string $descripcion, \DateTimeInterface|string|null $fechaInicio, \DateTimeInterface|string|null $fechaFin): void
    {
        $integracion = Integracion::where('id_tenant', $idTenant)->where('tipo', 'calendario')->first();

        if (! $integracion || $integracion->estado !== 'conectada') {
            return;
        }

        $accessToken = $this->obtenerAccessTokenValido($integracion);
        if (! $accessToken) {
            return;
        }

        $inicio = $fechaInicio ? Carbon::parse($fechaInicio) : now();
        $fin = $fechaFin ? Carbon::parse($fechaFin) : $inicio->copy()->addHour();

        $respuesta = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                'summary' => $titulo,
                'description' => $descripcion,
                'start' => ['dateTime' => $inicio->toRfc3339String()],
                'end' => ['dateTime' => $fin->toRfc3339String()],
            ]);

        if ($respuesta->failed()) {
            Log::warning('GoogleCalendarService: no se pudo crear el evento', [
                'id_tenant' => $idTenant,
                'status' => $respuesta->status(),
                'body' => $respuesta->body(),
            ]);
        }
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
