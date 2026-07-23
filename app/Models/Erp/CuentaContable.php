<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaContable extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_plan_cuentas';

    protected $fillable = [
        'id_tenant',
        'codigo',
        'nombre',
        'tipo',
        'naturaleza',
        'id_cuenta_padre',
        'es_movible',
        'activo',
    ];

    protected $casts = [
        'es_movible' => 'boolean',
        'activo' => 'boolean',
    ];

    public function cuentaPadre(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'id_cuenta_padre');
    }

    public function hijas(): HasMany
    {
        return $this->hasMany(CuentaContable::class, 'id_cuenta_padre');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class, 'id_cuenta');
    }
}
