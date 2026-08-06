<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class PedidoPago extends Model
{
    protected $table = 'erp_pedido_pagos';

    protected $fillable = [
        'id_pedido',
        'metodo_pago',
        'monto',
    ];

    protected $casts = [
        'monto' => 'float',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
