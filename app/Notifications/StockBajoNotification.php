<?php

namespace App\Notifications;

use App\Models\Producto;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBajoNotification extends Notification
{
    public function __construct(private Producto $producto) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Stock bajo: {$this->producto->nombre}")
            ->greeting('Alerta de inventario')
            ->line("El producto \"{$this->producto->nombre}\"" . ($this->producto->sku ? " (SKU {$this->producto->sku})" : '') . " llegó a un nivel de stock bajo.")
            ->line("Stock actual: {$this->producto->stock} — mínimo configurado: {$this->producto->stock_minimo}.")
            ->line('Revisa el inventario en STRATO para reponerlo a tiempo.');
    }
}
