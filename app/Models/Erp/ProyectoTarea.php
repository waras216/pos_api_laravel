<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_proyecto_tareas';

    protected $fillable = [
        'id_tenant',
        'id_proyecto',
        'titulo',
        'descripcion',
        'estado',
        'asignado',
        'orden',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin' => 'date:Y-m-d',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}
