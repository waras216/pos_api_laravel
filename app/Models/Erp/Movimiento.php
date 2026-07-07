<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'erp_movimientos';

    protected $fillable = [
        'id_tenant',
        'concepto',
        'tipo',
        'monto',
        'fecha',
        'categoria',
    ];

    protected $casts = [
        'monto' => 'float',
    ];
}
