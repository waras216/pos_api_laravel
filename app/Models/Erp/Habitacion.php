<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitacion extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'erp_habitaciones';

    protected $fillable = [
        'id_tenant',
        'numero',
        'tipo',
        'precio',
        'piso',
        'estado',
        'estado_limpieza',
        'huesped',
        'check_in',
        'check_out',
        'noches',
    ];

    protected $casts = [
        'precio' => 'float',
        'check_in' => 'date:Y-m-d',
        'check_out' => 'date:Y-m-d',
    ];

    public function consumos()
    {
        return $this->hasMany(HabitacionConsumo::class, 'id_habitacion');
    }

    public function estadias()
    {
        return $this->hasMany(Estadia::class, 'id_habitacion');
    }

    public function estadiaActiva()
    {
        return $this->hasOne(Estadia::class, 'id_habitacion')->where('estado', 'activa')->latestOfMany('check_in');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_habitacion');
    }
}
