<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_mesas';

    protected $fillable = [
        'id_tenant',
        'numero',
        'capacidad',
        'estado',
        'mesero',
    ];

    public function comandas()
    {
        return $this->hasMany(Comanda::class, 'id_mesa');
    }

    public function comandaActiva()
    {
        return $this->hasOne(Comanda::class, 'id_mesa')->where('estado', '!=', 'cerrada')->latestOfMany();
    }
}
