<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'erp_proyectos';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'cliente',
        'responsable',
        'estado',
        'progreso',
        'horas',
        'presupuesto',
    ];

    protected $casts = [
        'presupuesto' => 'float',
    ];

    public function tareas()
    {
        return $this->hasMany(ProyectoTarea::class, 'id_proyecto')->orderBy('orden');
    }

    public function registrosHoras()
    {
        return $this->hasMany(ProyectoHora::class, 'id_proyecto');
    }
}
