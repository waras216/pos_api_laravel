<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominaPago extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_nomina_pagos';

    protected $fillable = [
        'id_tenant',
        'fecha',
        'total',
        'id_asiento',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'id_asiento');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(NominaPagoDetalle::class, 'id_nomina_pago');
    }
}
