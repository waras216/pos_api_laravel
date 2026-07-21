<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class ComandaItem extends Model
{
    protected $table = 'erp_comanda_items';

    protected $fillable = [
        'id_comanda',
        'id_producto',
        'nombre',
        'precio_unitario',
        'cantidad',
    ];

    protected $casts = [
        'precio_unitario' => 'float',
    ];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class, 'id_comanda');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }
}
