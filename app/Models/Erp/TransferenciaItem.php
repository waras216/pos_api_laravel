<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class TransferenciaItem extends Model
{
    protected $table = 'erp_transferencia_items';

    protected $fillable = ['id_transferencia', 'id_producto', 'cantidad'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }
}
