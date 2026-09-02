<?php

namespace App\Models\Erp;

use App\Models\Cliente;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_reservas';

    protected $fillable = [
        'id_tenant',
        'id_habitacion',
        'huesped',
        'id_cliente',
        'telefono',
        'fecha_checkin',
        'fecha_checkout',
        'noches',
        'notas',
        'estado',
    ];

    protected $casts = [
        'fecha_checkin' => 'date:Y-m-d',
        'fecha_checkout' => 'date:Y-m-d',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
