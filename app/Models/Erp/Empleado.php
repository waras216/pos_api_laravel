<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use BelongsToTenant;

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
