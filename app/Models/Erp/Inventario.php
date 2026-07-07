<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use SoftDeletes;

    protected $table = 'erp_inventario';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'sku',
        'categoria',
        'stock',
        'stock_minimo',
        'precio_compra',
        'precio_venta',
    ];

    protected $casts = [
        'precio_compra' => 'float',
        'precio_venta' => 'float',
    ];
}
