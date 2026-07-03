<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_tiponegocio';

    protected $fillable = [
        'nombre_negocio',
        'slug',
    ];

        public function tenants()
    {
        return $this->hasMany(Tenant::class, 'id_tiponegocio');
    }
}
