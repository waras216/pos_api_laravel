<?php

namespace App\Services\Whatsapp;

/**
 * Driver activo mientras no haya un proveedor de WhatsApp real conectado
 * (WHATSAPP_DRIVER=null, el default). Nunca finge un envío exitoso —
 * a diferencia de un email, un WhatsApp "fantasma" no tiene ni siquiera un
 * log verificable del lado del usuario, así que el silencio sería el peor
 * resultado posible. Falla explícito para que quede claro que hace falta
 * verificar un número de WhatsApp Business y cargar credenciales reales.
 */
class NullWhatsappDriver implements WhatsappDriverInterface
{
    public function enviar(int $idTenant, string $telefono, string $mensaje): void
    {
        throw new WhatsappException(
            'No hay un proveedor de WhatsApp configurado. Define WHATSAPP_DRIVER y las ' .
            'credenciales correspondientes en .env (ver config/whatsapp.php) — requiere un ' .
            'número de WhatsApp Business verificado por Meta o una cuenta de Twilio.'
        );
    }
}
