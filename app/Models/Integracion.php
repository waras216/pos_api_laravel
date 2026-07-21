<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Integracion extends Model
{
    use BelongsToTenant;

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
