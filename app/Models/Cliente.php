<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Erp\Pedido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'apellido_p',
        'apellido_m',
        'email',
        'telefono',
        'empresa',
        'sector_empresarial',
        'rfc',
        'direccion',
        'tipo',
        'activo',
        'limite_credito',
        'saldo_credito',
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'saldo_credito' => 'decimal:2',
    ];

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'id_cliente', 'id_cliente');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'id_cliente', 'id_cliente');
    }

    public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class, 'id_cliente', 'id_cliente');        
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_cliente', 'id_cliente');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cliente', 'id_cliente');
    }
}
