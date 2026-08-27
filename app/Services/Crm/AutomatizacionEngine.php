<?php

namespace App\Services\Crm;

use App\Models\Actividad;
use App\Models\Automatizacion;
use App\Models\Notificacion;
use App\Models\Rol;
use App\Models\Usuarios;
use App\Notifications\AutomatizacionEmailNotification;
use App\Services\GoogleCalendarService;
use App\Services\IntegracionService;
use App\Services\Whatsapp\WhatsappDriverInterface;
use App\Services\Whatsapp\WhatsappException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Ejecuta las Automatizacion activas de un tenant cuando ocurre un evento
 * real del CRM. Se invoca explícitamente desde los controllers en el punto
 * exacto donde pasa el evento (LeadController::store, OportunidadController
 * ::moverEtapa, el comando programado de actividades vencidas) — no hay
 * listeners genéricos de modelo, así que una acción que modifica un Lead u
 * Oportunidad (ver cambiarEstado()) nunca puede re-disparar el motor por
 * accidente y entrar en loop.
 */
class AutomatizacionEngine
{
    public const EVENTOS = [
        'lead_creado',
        'oportunidad_ganada',
        'oportunidad_perdida',
        'oportunidad_etapa_cambiada',
        'actividad_vencida',
    ];

    public const ACCIONES = [
        'enviar_email',
        'crear_actividad',
        'cambiar_estado',
        'notificar_usuario',
        'enviar_whatsapp',
    ];

    public function __construct(private WhatsappDriverInterface $whatsapp, private GoogleCalendarService $calendario) {}

    /**
     * @param  array<string, mixed>  $contexto  claves posibles: 'lead' (Lead), 'oportunidad' (Oportunidad), 'actividad' (Actividad)
     */
    public function disparar(string $evento, int $idTenant, array $contexto): void
    {
        $automatizaciones = Automatizacion::where('id_tenant', $idTenant)
            ->where('evento', $evento)
            ->where('activa', true)
            ->get();

        foreach ($automatizaciones as $automatizacion) {
            try {
                $this->ejecutar($automatizacion, $contexto);
            } catch (\Throwable $e) {
                // Una automatización mal configurada (ej. el usuario asignado
                // ya no existe) no debe tumbar la operación de negocio que la
                // disparó -- se loguea y se sigue con las demás.
                Log::warning("Automatización #{$automatizacion->id} falló al ejecutarse", [
                    'evento' => $evento,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function ejecutar(Automatizacion $automatizacion, array $contexto): void
    {
        match ($automatizacion->accion) {
            'enviar_email' => $this->enviarEmail($automatizacion, $contexto),
            'crear_actividad' => $this->crearActividad($automatizacion, $contexto),
            'cambiar_estado' => $this->cambiarEstado($automatizacion, $contexto),
            'notificar_usuario' => $this->notificarUsuario($automatizacion, $contexto),
            'enviar_whatsapp' => $this->enviarWhatsapp($automatizacion, $contexto),
            default => null,
        };
    }

    private function contactoEmail(array $contexto): ?string
    {
        if (isset($contexto['lead'])) {
            return $contexto['lead']->email;
        }
        if (isset($contexto['oportunidad'])) {
            return $contexto['oportunidad']->cliente?->email;
        }
        if (isset($contexto['actividad'])) {
            return $contexto['actividad']->cliente?->email ?? $contexto['actividad']->lead?->email;
        }

        return null;
    }

    private function contactoTelefono(array $contexto): ?string
    {
        if (isset($contexto['lead'])) {
            return $contexto['lead']->telefono;
        }
        if (isset($contexto['oportunidad'])) {
            return $contexto['oportunidad']->cliente?->telefono;
        }
        if (isset($contexto['actividad'])) {
            return $contexto['actividad']->cliente?->telefono ?? $contexto['actividad']->lead?->telefono;
        }

        return null;
    }

    private function resolverIdCliente(array $contexto): ?int
    {
        if (isset($contexto['lead'])) {
            return $contexto['lead']->id_cliente;
        }
        if (isset($contexto['oportunidad'])) {
            return $contexto['oportunidad']->id_cliente;
        }
        if (isset($contexto['actividad'])) {
            return $contexto['actividad']->id_cliente;
        }

        return null;
    }

    private function enviarEmail(Automatizacion $automatizacion, array $contexto): void
    {
        if (! IntegracionService::conectada($automatizacion->id_tenant, 'email')) {
            return;
        }

        $params = $automatizacion->parametros ?? [];
        $asunto = $params['asunto'] ?? $automatizacion->nombre_automatizacion;
        $mensaje = $params['mensaje'] ?? '';
        $destinatario = $params['destinatario'] ?? 'contacto';

        $emails = [];

        if ($destinatario === 'admins') {
            $idsAdmin = Rol::idsAdminTenant($automatizacion->id_tenant);
            $emails = Usuarios::whereIn('id_usuario', $idsAdmin)->whereNotNull('email')->pluck('email')->all();
        } else {
            $email = $this->contactoEmail($contexto);
            if ($email) {
                $emails = [$email];
            }
        }

        foreach ($emails as $email) {
            Notification::route('mail', $email)->notify(new AutomatizacionEmailNotification($asunto, $mensaje));
        }
    }

    private function crearActividad(Automatizacion $automatizacion, array $contexto): void
    {
        $params = $automatizacion->parametros ?? [];

        $idCliente = null;
        $idLead = null;
        $idOportunidad = null;
        $idUsuarioDueno = null;

        if (isset($contexto['lead'])) {
            $idLead = $contexto['lead']->id_lead;
            $idCliente = $contexto['lead']->id_cliente;
            $idUsuarioDueno = $contexto['lead']->id_usuario;
        } elseif (isset($contexto['oportunidad'])) {
            $idOportunidad = $contexto['oportunidad']->id_oportunidad;
            $idCliente = $contexto['oportunidad']->id_cliente;
            $idUsuarioDueno = $contexto['oportunidad']->id_usuario;
        } elseif (isset($contexto['actividad'])) {
            $idCliente = $contexto['actividad']->id_cliente;
            $idLead = $contexto['actividad']->id_lead;
            $idOportunidad = $contexto['actividad']->id_oportunidad;
            $idUsuarioDueno = $contexto['actividad']->id_usuario;
        }

        $fechaInicio = now();
        $fechaFin = now()->addDays((int) ($params['dias_vencimiento'] ?? 1));

        $actividad = Actividad::create([
            'id_tenant' => $automatizacion->id_tenant,
            'id_usuario' => $params['id_usuario_asignado'] ?? $idUsuarioDueno,
            'id_cliente' => $idCliente,
            'id_lead' => $idLead,
            'id_oportunidad' => $idOportunidad,
            'tipo' => $params['tipo'] ?? 'tarea',
            'titulo' => $params['titulo'] ?? $automatizacion->nombre_automatizacion,
            'estado' => 'pendiente',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
        ]);

        $idEventoGoogle = $this->calendario->crearEvento($automatizacion->id_tenant, $actividad->titulo, $actividad->descripcion, $fechaInicio, $fechaFin);
        if ($idEventoGoogle) {
            $actividad->update(['id_evento_google' => $idEventoGoogle]);
        }
    }

    private function cambiarEstado(Automatizacion $automatizacion, array $contexto): void
    {
        $valor = $automatizacion->parametros['valor'] ?? null;
        if (! $valor) {
            return;
        }

        if (isset($contexto['lead'])) {
            $contexto['lead']->update(['estado' => $valor]);
        } elseif (isset($contexto['oportunidad'])) {
            $contexto['oportunidad']->update(['etapa' => $valor]);
        }
    }

    private function enviarWhatsapp(Automatizacion $automatizacion, array $contexto): void
    {
        if (! IntegracionService::conectada($automatizacion->id_tenant, 'whatsapp')) {
            return;
        }

        $telefono = $this->contactoTelefono($contexto);
        if (! $telefono) {
            return;
        }

        $mensaje = $automatizacion->parametros['mensaje'] ?? '';

        try {
            $this->whatsapp->enviar($automatizacion->id_tenant, $telefono, $mensaje);
        } catch (WhatsappException $e) {
            Log::warning("Automatización #{$automatizacion->id}: envío de WhatsApp falló", ['error' => $e->getMessage()]);
        }
    }

    private function notificarUsuario(Automatizacion $automatizacion, array $contexto): void
    {
        $params = $automatizacion->parametros ?? [];
        $idUsuario = $params['id_usuario'] ?? null;
        if (! $idUsuario) {
            return;
        }

        $usuario = Usuarios::find($idUsuario);
        if (! $usuario) {
            return;
        }

        $mensaje = $params['mensaje'] ?? "Se disparó la automatización \"{$automatizacion->nombre_automatizacion}\".";

        Notificacion::create([
            'id_tenant' => $automatizacion->id_tenant,
            'id_usuario' => $usuario->id_usuario,
            'id_cliente' => $this->resolverIdCliente($contexto),
            'titulo' => $automatizacion->nombre_automatizacion,
            'mensaje' => $mensaje,
            'tipo' => 'info',
        ]);

        if ($usuario->email && IntegracionService::conectada($automatizacion->id_tenant, 'email')) {
            Notification::route('mail', $usuario->email)->notify(new AutomatizacionEmailNotification($automatizacion->nombre_automatizacion, $mensaje));
        }
    }
}
