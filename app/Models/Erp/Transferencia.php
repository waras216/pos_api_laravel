<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_transferencias';

    protected $fillable = [
        'id_tenant', 'id_sucursal_origen', 'id_sucursal_destino', 'id_usuario',
        'fecha', 'estado', 'notas',
    ];

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal_origen', 'id_sucursal');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal_destino', 'id_sucursal');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

    public function items()
    {
        return $this->hasMany(TransferenciaItem::class, 'id_transferencia');
    }
}
