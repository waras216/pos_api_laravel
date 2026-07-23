<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominaPagoDetalle extends Model
{
    protected $table = 'erp_nomina_pago_detalles';

    protected $fillable = [
        'id_nomina_pago',
        'id_empleado',
        'salario',
    ];

    protected $casts = [
        'salario' => 'float',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}
