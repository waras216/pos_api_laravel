<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Categoria extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'descripcion',
        'activo',
    ];

    public function productos(){
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}
