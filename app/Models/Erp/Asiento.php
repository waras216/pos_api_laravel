<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asiento extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_asientos';

    protected $fillable = [
        'id_tenant',
        'fecha',
        'concepto',
        'origen',
        'referencia_tipo',
        'referencia_id',
        'total_debe',
        'total_haber',
        'id_usuario',
    ];

    protected $casts = [
        'total_debe' => 'float',
        'total_haber' => 'float',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class, 'id_asiento');
    }
}
