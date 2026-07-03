<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampanaMarketing extends Model
{
    use SoftDeletes;

    protected $table = 'campanas_marketing';

    protected $fillable = [
        'id_tenant',
        'id_usuario',
        'nombre_compania',
        'segmento',
        'estado',
        'fecha_inicio',
        'lista_contactos',
    ];

    protected $casts = [
        'lista_contactos' => 'array',
        'fecha_inicio' => 'date:Y-m-d',
    ];

    protected $appends = [
        'n_contacto',
    ];

    public function getNContactoAttribute(): int
    {
        return count($this->lista_contactos ?? []);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
