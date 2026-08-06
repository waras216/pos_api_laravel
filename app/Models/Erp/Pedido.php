<?php

namespace App\Models\Erp;

use App\Models\Cliente;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_pedidos_venta';

    protected $fillable = [
        'id_tenant',
        'id_cliente',
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class, 'id_pedido');
    }

    public function pagos()
    {
        return $this->hasMany(PedidoPago::class, 'id_pedido');
    }
}
