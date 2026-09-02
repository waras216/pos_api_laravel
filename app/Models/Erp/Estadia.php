<?php

namespace App\Models\Erp;

use App\Models\Cliente;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Estadia extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_estadias';

    protected $fillable = [
        'id_tenant',
        'id_habitacion',
        'huesped',
        'id_cliente',
        'check_in',
        'check_out_programado',
        'noches',
        'check_out_real',
        'total_hospedaje',
        'total_consumos',
        'total',
        'id_pedido',
        'estado',
        'documento_tipo',
        'documento_numero',
        'firma',
        'firmado_at',
    ];

    protected $casts = [
        'check_in' => 'date:Y-m-d',
        'check_out_programado' => 'date:Y-m-d',
        'check_out_real' => 'datetime',
        'total_hospedaje' => 'float',
        'total_consumos' => 'float',
        'total' => 'float',
        'firmado_at' => 'datetime',
    ];

    protected $appends = ['firma_url'];

    public function getFirmaUrlAttribute(): ?string
    {
        return $this->firma ? \Storage::disk('public')->url($this->firma) : null;
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
