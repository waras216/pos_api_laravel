<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HabitacionIncidencia extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_habitacion_incidencias';

    protected $fillable = [
        'id_tenant',
        'id_habitacion',
        'titulo',
        'descripcion',
        'prioridad',
        'fuera_de_servicio',
        'estado',
        'resuelta_at',
    ];

    protected $casts = [
        'fuera_de_servicio' => 'boolean',
        'resuelta_at' => 'datetime',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }
}
