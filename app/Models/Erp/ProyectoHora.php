<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class ProyectoHora extends Model
{
    protected $table = 'erp_proyecto_horas';

    protected $fillable = [
        'id_tenant',
        'id_proyecto',
        'colaborador',
        'fecha',
        'horas',
        'descripcion',
    ];

    protected $casts = [
        'horas' => 'float',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}
