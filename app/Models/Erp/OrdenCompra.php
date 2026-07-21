<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_ordenes_compra';

    protected $fillable = [
        'id_tenant',
        'id_proveedor',
        'fecha',
        'estado',
        'total',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function items()
    {
        return $this->hasMany(OrdenCompraItem::class, 'id_orden_compra');
    }
}
