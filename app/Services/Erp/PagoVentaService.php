<?php

namespace App\Services\Erp;

use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoPago;
use Illuminate\Validation\ValidationException;

class PagoVentaService
{
    public const METODOS = ['efectivo', 'tarjeta_debito', 'tarjeta_credito'];

    /**
     * @param  array<int, array{metodo_pago:string, monto:float}>  $pagos
     */
    public function validar(array $pagos, float $total): void
    {
        $suma = round(array_sum(array_column($pagos, 'monto')), 2);

        if ($suma !== round($total, 2)) {
            throw ValidationException::withMessages([
                'pagos' => "La suma de los pagos ({$suma}) no coincide con el total de la venta ({$total}).",
            ]);
        }
    }

    /**
     * @param  array<int, array{metodo_pago:string, monto:float}>  $pagos
     */
    public function crear(Pedido $pedido, array $pagos): void
    {
        foreach ($pagos as $pago) {
            PedidoPago::create([
                'id_pedido' => $pedido->id,
                'metodo_pago' => $pago['metodo_pago'],
                'monto' => $pago['monto'],
            ]);
        }
    }
}
