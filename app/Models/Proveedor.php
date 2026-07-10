<?php

namespace App\Models;

use App\Models\Erp\OrdenCompra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'contacto',
        'email',
        'telefono',
        'direccion',
        'rfc',
        'activo',
    ];

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_proveedor', 'id_proveedor');
    }
}
