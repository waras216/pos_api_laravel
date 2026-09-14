<?php

namespace App\Models\Erp;

use App\Models\Cliente;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Model;

class MovimientoCredito extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_movimientos_credito';

    protected $fillable = [
        'id_tenant', 'id_cliente', 'id_usuario', 'tipo', 'monto',
        'saldo_resultante', 'referencia', 'notas', 'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
