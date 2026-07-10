<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
    use SoftDeletes;

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
