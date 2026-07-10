<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_membresia';

    protected $fillable = [
        'id_usuario',
        'id_tenant',
        'estado',
        'es_owner',
        'invitado_por',
        'unido_en',
    ];

    protected $casts = [
        'es_owner' => 'boolean',
        'unido_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }
}
