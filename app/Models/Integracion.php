<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integracion extends Model
{
    protected $table = 'integraciones';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'tipo',
        'estado',
        'configuracion',
    ];

    protected $casts = [
        'configuracion' => 'array',
    ];
}
