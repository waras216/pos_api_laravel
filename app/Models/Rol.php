<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'nombre_rol'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'id_usuario');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permiso::class, 'id_permiso');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }
}
