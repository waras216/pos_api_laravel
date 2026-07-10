<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraItem extends Model
{
    protected $table = 'erp_orden_compra_items';

    protected $fillable = [
        'id_orden_compra',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'float',
        'subtotal' => 'float',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_orden_compra');
    }
}
