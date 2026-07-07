<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'erp_ordenes_compra';

    protected $fillable = [
        'id_tenant',
        'proveedor',
        'fecha',
        'estado',
        'items',
        'total',
    ];

    protected $casts = [
        'total' => 'float',
    ];
}
