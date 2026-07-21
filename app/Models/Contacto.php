<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'contactos';
    protected $primaryKey = 'id_contacto';

    protected $fillable = [
        'id_tenant',
        'id_cliente',
        'nombre',
        'apellido_p',
        'apellido_m',
        'email',
        'telefono',
        'cargo',
        'principal',        
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
