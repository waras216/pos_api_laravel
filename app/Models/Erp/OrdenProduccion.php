<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_ordenes_produccion';

    protected $fillable = [
        'id_tenant',
        'producto',
        'cantidad',
        'progreso',
        'estado',
    ];
}
