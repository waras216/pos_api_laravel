<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'erp_movimientos_stock';

    protected $fillable = [
        'id_tenant',
        'id_producto',
        'tipo',
        'cantidad',
        'motivo',
        'referencia',
        'stock_resultante',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }
}
