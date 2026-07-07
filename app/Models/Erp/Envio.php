<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'erp_envios';

    protected $fillable = [
        'id_tenant',
        'destino',
        'transportista',
        'eta',
        'estado',
    ];
}
