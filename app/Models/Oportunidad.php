<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oportunidad extends Model
{
    use SoftDeletes;

    protected $table = 'oportunidades';
    protected $primaryKey = 'id_oportunidad';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_tenant',
        'id_cliente',
        'id_pipeline',
        'id_usuario',
        'titulo',
        'valor',
        'probabilidad',
        'estado',
        'etapa',
        'fecha_cierre',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');        
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'id_pipeline', 'id_pipeline');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');  
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_oportunidad', 'id_oportunidad');
    }
}
