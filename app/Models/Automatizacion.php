<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Automatizacion extends Model
{
    use BelongsToTenant;

    protected $table = 'automatizaciones';

    protected $fillable = [
        'id_tenant',
        'nombre_automatizacion',
        'regla',
        'evento',
        'accion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    protected $appends = ['fecha_creacion'];

    public function getFechaCreacionAttribute(): ?string
    {
        return $this->created_at?->toDateString();
    }
}
