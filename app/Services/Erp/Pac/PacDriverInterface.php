<?php

namespace App\Services\Erp\Pac;

use App\Models\Erp\Factura;

/**
 * Punto de integración con un PAC (Proveedor Autorizado de Certificación)
 * real. Cada driver concreto (Facturama, SW Sapien, Finkok, ...) traduce
 * una Factura interna a la llamada específica de su API y devuelve el
 * folio fiscal (UUID) que el SAT reconoce como válido.
 *
 * timbrar() debe lanzar PacException (o una subclase) en cualquier falla
 * — credenciales inválidas, PAC no configurado, rechazo del SAT, etc. —
 * para que FacturaService pueda dejar la Factura en estado "error" con un
 * mensaje explicable en vez de fingir un timbrado que no ocurrió.
 */
interface PacDriverInterface
{
    public function timbrar(Factura $factura): PacTimbradoResultado;
}
