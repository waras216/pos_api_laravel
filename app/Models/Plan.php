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
        'stripe_price_id',
        'max_usuarios',
        'incluye_facturacion_real',
        'fecha_inicio',
        'fecha_fin'
    ];

    protected $casts = [
        'incluye_facturacion_real' => 'boolean',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'id_plan');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'id_plan', 'id_plan');
    }

}
