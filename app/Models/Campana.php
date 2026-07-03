<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campana extends Model
{
    protected $table = 'campanas';

    protected $fillable = [
        'id_tenant',
        'nombre_compania',
        'segmento',
        'estado',
        'fecha_inicio',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
    ];

    protected $appends = ['n_contacto'];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'campana_cliente', 'id_campana', 'id_cliente');
    }

    public function getNContactoAttribute(): int
    {
        return $this->relationLoaded('clientes') ? $this->clientes->count() : $this->clientes()->count();
    }
}
