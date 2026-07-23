<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsientoDetalle extends Model
{
    protected $table = 'erp_asiento_detalles';

    protected $fillable = [
        'id_asiento',
        'id_cuenta',
        'debe',
        'haber',
        'descripcion',
    ];

    protected $casts = [
        'debe' => 'float',
        'haber' => 'float',
    ];

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'id_asiento');
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'id_cuenta');
    }
}
