<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_plan';

    protected $fillable = [
        'nombre_plan',
        'precio',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'id_plan');
    }

}
