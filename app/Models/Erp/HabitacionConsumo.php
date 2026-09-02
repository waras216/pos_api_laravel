<?php

namespace App\Models\Erp;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class HabitacionConsumo extends Model
{
    protected $table = 'erp_habitacion_consumos';

    protected $fillable = [
        'id_habitacion',
        'id_producto',
        'nombre',
        'seccion',
        'precio_unitario',
        'cantidad',
    ];

    protected $casts = [
        'precio_unitario' => 'float',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }
}
