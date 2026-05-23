<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'leads';
    protected $primaryKey = 'id_lead';

    protected $fillable = [
        'id_tenant',
        'id_cliente',
        'id_usuario',
        'titulo',
        'descripcion',
        'estado',
        'fuente',
        'valor_estimado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');        
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_lead', 'id_lead');
    }
}
