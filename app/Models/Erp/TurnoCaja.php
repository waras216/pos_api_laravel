<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Model;

class TurnoCaja extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_turnos_caja';
    protected $primaryKey = 'id_turno';

    protected $fillable = [
        'id_tenant', 'id_caja', 'id_usuario', 'fecha_apertura', 'fecha_cierre',
        'monto_apertura', 'monto_cierre', 'diferencia', 'estado', 'notas',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }

    public function cajero()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
