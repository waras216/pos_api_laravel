<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_cajas';
    protected $primaryKey = 'id_caja';

    protected $fillable = [
        'id_tenant', 'id_sucursal', 'nombre', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function turnos()
    {
        return $this->hasMany(TurnoCaja::class, 'id_caja', 'id_caja');
    }
}
