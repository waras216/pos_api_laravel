<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SolicitudHuesped extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_solicitudes_huesped';

    protected $fillable = [
        'id_tenant',
        'id_habitacion',
        'huesped',
        'titulo',
        'descripcion',
        'categoria',
        'prioridad',
        'estado',
        'resuelta_at',
    ];

    protected $casts = [
        'resuelta_at' => 'datetime',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }
}
