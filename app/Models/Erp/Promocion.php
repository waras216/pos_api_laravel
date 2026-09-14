<?php

namespace App\Models\Erp;

use App\Models\Categoria;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_promociones';

    protected $fillable = [
        'id_tenant', 'nombre', 'tipo', 'valor', 'id_producto', 'id_categorias',
        'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_productos');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categorias', 'id_categoria');
    }
}
