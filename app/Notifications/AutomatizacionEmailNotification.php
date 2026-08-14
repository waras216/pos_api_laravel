<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Correo genérico enviado por una Automatizacion del CRM (asunto/mensaje
 * definidos por el usuario al crear la regla, ver AutomatizacionEngine).
 */
class AutomatizacionEmailNotification extends Notification
{
    public function __construct(private string $asunto, private string $mensaje)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->asunto)
            ->line($this->mensaje);
    }
}
