<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;

/**
 * Habla con el sidecar de whatsapp_baileys_service/ (Node + Baileys) por
 * HTTP interno. Automatización no oficial de WhatsApp Web -- ver el aviso
 * en whatsapp_baileys_service/index.js sobre el riesgo de baneo.
 */
class BaileysDriver implements WhatsappDriverInterface
{
    public function __construct(private array $config) {}

    public function enviar(int $idTenant, string $telefono, string $mensaje): void
    {
        $respuesta = Http::baseUrl($this->config['url'])
            ->timeout(15)
            ->post("/sesiones/{$idTenant}/enviar", [
                'telefono' => $telefono,
                'mensaje' => $mensaje,
            ]);

        if ($respuesta->failed()) {
            throw new WhatsappException(
                $respuesta->json('error') ?? 'No se pudo enviar el WhatsApp (sesión de Baileys no disponible).'
            );
        }
    }
}
