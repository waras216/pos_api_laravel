<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'apellido_p',
        'apellido_m',
        'email',
        'telefono',
        'rfc',
        'direccion',
        'tipo',
        'activo',        
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
}
