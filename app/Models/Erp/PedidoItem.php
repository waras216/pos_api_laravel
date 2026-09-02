<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'erp_pedido_items';

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'descripcion',
        'seccion',
        'cantidad',
        'precio_unitario',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'float',
        'costo_unitario' => 'float',
        'subtotal' => 'float',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
