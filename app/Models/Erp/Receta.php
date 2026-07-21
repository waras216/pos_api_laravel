<?php

namespace App\Models\Erp;

use App\Models\Cliente;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_recetas';

    protected $fillable = [
        'id_tenant',
        'id_cliente',
        'id_producto',
        'dosis',
        'cantidad',
        'pendiente',
    ];

    protected $casts = [
        'pendiente' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }
}
