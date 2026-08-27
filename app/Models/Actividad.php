<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'id_actividad',
        'id_tenant',
        'id_usuario',
        'id_cliente',
        'id_lead',
        'id_oportunidad',
        'tipo',
        'titulo',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'automatizacion_disparada',
        'id_evento_google',
    ];

    protected $casts = [
        'automatizacion_disparada' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'id_lead', 'id_lead');
    }
    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class, 'id_oportunidad', 'id_oportunidad');
    }
    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
