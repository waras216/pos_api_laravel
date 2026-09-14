<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Producto;
use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_devoluciones';

    protected $fillable = [
        'id_tenant', 'id_pedido', 'id_producto', 'id_usuario', 'cantidad',
        'tipo', 'motivo', 'estado', 'monto_reembolso', 'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
