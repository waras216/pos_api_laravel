<?php

namespace App\Services\Erp\Pac;

use App\Models\Erp\Factura;

/**
 * Driver activo mientras no haya un PAC real conectado (PAC_DRIVER=null,
 * el default). Nunca genera un UUID falso — eso produciría un "comprobante
 * fiscal" con apariencia de válido pero sin ningún valor legal ante el
 * SAT — y en cambio falla explícitamente para que el usuario sepa que
 * necesita contratar y configurar un PAC antes de timbrar de verdad.
 */
class NullPacDriver implements PacDriverInterface
{
    public function timbrar(Factura $factura): PacTimbradoResultado
    {
        throw new PacException(
            'No hay un PAC configurado para timbrado real. Define PAC_DRIVER y las ' .
            'credenciales correspondientes en .env (ver config/pac.php), o usa el modo ' .
            '"Solo registro" mientras tanto.'
        );
    }
}
