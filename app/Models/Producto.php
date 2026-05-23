<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    
    protected $table = 'productos';
    protected $primaryKey = 'id_productos';
    public $incrementing = true;

    protected $fillable = [
        'id_tenant',
        'id_categorias',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'codigo_barras',
        'activo'
    ];

    public function categoria(){
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }
    
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($this->primaryKey, $value)->firstOrFail();
    }
}
