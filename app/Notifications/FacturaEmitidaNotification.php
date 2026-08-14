<?php

namespace App\Notifications;

use App\Models\Erp\Factura;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacturaEmitidaNotification extends Notification
{
    public function __construct(private Factura $factura) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mensaje = (new MailMessage)
            ->subject("Factura {$this->factura->serie}-{$this->factura->folio}")
            ->greeting("Hola {$this->factura->razon_social_receptor}")
            ->line($this->factura->tipo === 'timbrada'
                ? 'Tu comprobante fiscal (CFDI) ya fue timbrado.'
                : 'Se registró tu comprobante de compra.')
            ->line("Folio: {$this->factura->serie}-{$this->factura->folio}")
            ->line("Total: \${$this->factura->total}");

        if ($this->factura->uuid) {
            $mensaje->line("UUID fiscal: {$this->factura->uuid}");
        }

        if ($this->factura->pdf_path) {
            $mensaje->attach(storage_path('app/public/' . $this->factura->pdf_path));
        }

        return $mensaje->line('Gracias por tu compra.');
    }
}
