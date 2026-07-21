<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Envio extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'erp_envios';

    protected $fillable = [
        'id_tenant',
        'destino',
        'transportista',
        'eta',
        'estado',
    ];
}
