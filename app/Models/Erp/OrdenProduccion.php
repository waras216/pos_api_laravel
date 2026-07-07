<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    protected $table = 'erp_ordenes_produccion';

    protected $fillable = [
        'id_tenant',
        'producto',
        'cantidad',
        'progreso',
        'estado',
    ];
}
