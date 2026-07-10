<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_modulo';

    protected $fillable = [
        'clave',
        'nombre',
    ];

    public function roles()
    {
        return $this->hasMany(Rol::class, 'id_modulo', 'id_modulo');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_modulo', 'id_modulo');
    }
}
