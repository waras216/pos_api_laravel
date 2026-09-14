<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_sucursales';
    protected $primaryKey = 'id_sucursal';

    protected $fillable = [
        'id_tenant', 'nombre', 'direccion', 'telefono', 'responsable', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cajas()
    {
        return $this->hasMany(Caja::class, 'id_sucursal', 'id_sucursal');
    }
}
