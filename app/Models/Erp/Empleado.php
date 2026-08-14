<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'erp_empleados';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'departamento',
        'puesto',
        'estado',
        'salario',
    ];

    protected $casts = [
        'salario' => 'float',
    ];
}
