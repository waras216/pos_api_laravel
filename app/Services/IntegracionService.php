<?php

namespace App\Services;

use App\Models\Integracion;

/**
 * Único punto que responde "¿este tenant tiene la integración X conectada?".
 * Antes del toggle en Integraciones era puramente decorativo (activar/
 * desactivar no cambiaba nada); ahora los emisores reales (correos de
 * automatizaciones, facturas, invitaciones, alertas) lo consultan antes de
 * mandar algo, para que apagar una integración tenga un efecto de verdad.
 */
class IntegracionService
{
    public static function conectada(int $idTenant, string $tipo): bool
    {
        return Integracion::where('id_tenant', $idTenant)
            ->where('tipo', $tipo)
            ->where('estado', 'conectada')
            ->exists();
    }
}
