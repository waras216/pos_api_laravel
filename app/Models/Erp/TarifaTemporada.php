<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TarifaTemporada extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_tarifas_temporada';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'tipo_ajuste',
        'valor',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin' => 'date:Y-m-d',
        'valor' => 'float',
    ];
}
