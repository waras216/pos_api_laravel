<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitacionEquipoNotification extends Notification
{
    /**
     * @param  bool  $cuentaExistente  true si el usuario ya tenía una cuenta STRATO
     *   en otra empresa y solo se lo sumó como membresía nueva (no se le creó
     *   contraseña acá, así que no tiene sentido pedirle que la use).
     */
    public function __construct(private Tenant $tenant, private string $invitadoPor, private bool $cuentaExistente) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mensaje = (new MailMessage)
            ->subject("Te agregaron al equipo de {$this->tenant->nombre_tenant} en STRATO")
            ->greeting("¡Hola {$notifiable->nombre}!")
            ->line("{$this->invitadoPor} te agregó al equipo de \"{$this->tenant->nombre_tenant}\" en STRATO.");

        return $this->cuentaExistente
            ? $mensaje->line('Ya tenés una cuenta en STRATO: entrá con tu correo y contraseña de siempre, y vas a poder cambiarte a esta empresa desde el selector de empresas.')
            : $mensaje->line("Ya podés entrar con tu correo ({$notifiable->email}) y la contraseña que te compartió {$this->invitadoPor}.");
    }
}
