<?php

namespace App\Services\Whatsapp;

/**
 * Punto de integración con un proveedor de WhatsApp real (Meta Cloud API,
 * Twilio, ...). Mismo patrón que App\Services\Erp\Pac\PacDriverInterface:
 * un driver concreto traduce el mensaje a la llamada específica de su API,
 * y enviar() lanza WhatsappException en cualquier falla (número no
 * verificado, plantilla no aprobada, credenciales inválidas, etc.) para
 * que el llamador decida qué hacer en vez de fingir un envío que no ocurrió.
 */
interface WhatsappDriverInterface
{
    /**
     * $idTenant solo lo usa el driver de Baileys (una sesión de WhatsApp
     * Web por tenant); los drivers de cuenta única (Meta Cloud API,
     * Twilio) lo ignoran.
     */
    public function enviar(int $idTenant, string $telefono, string $mensaje): void;
}
