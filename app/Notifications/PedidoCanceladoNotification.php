<?php

namespace App\Notifications;

use App\Models\Erp\Pedido;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoCanceladoNotification extends Notification
{
    public function __construct(private Pedido $pedido) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pedido #{$this->pedido->id} cancelado")
            ->greeting('Alerta de ventas')
            ->line("Se canceló el pedido #{$this->pedido->id}" . ($this->pedido->cliente ? " de {$this->pedido->cliente->nombre}" : '') . '.')
            ->line("Total: \${$this->pedido->total}.")
            ->line('Revisa el detalle en ERP → Ventas si necesitas dar seguimiento.');
    }
}
