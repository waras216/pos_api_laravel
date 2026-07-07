<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'erp_pedidos_venta';

    protected $fillable = [
        'id_tenant',
        'cliente',
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
    ];
}
