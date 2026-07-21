<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_habitaciones';

    protected $fillable = [
        'id_tenant',
        'numero',
        'tipo',
        'piso',
        'estado',
        'huesped',
        'check_in',
        'check_out',
        'noches',
    ];

    protected $casts = [
        'check_in' => 'date:Y-m-d',
        'check_out' => 'date:Y-m-d',
    ];

    public function consumos()
    {
        return $this->hasMany(HabitacionConsumo::class, 'id_habitacion');
    }
}
