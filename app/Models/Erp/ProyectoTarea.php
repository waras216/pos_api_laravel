<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    protected $table = 'erp_proyecto_tareas';

    protected $fillable = [
        'id_tenant',
        'id_proyecto',
        'titulo',
        'descripcion',
        'estado',
        'asignado',
        'orden',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}
