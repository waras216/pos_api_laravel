<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'password',
    ];

    public function tenant()
    {
        return $this->hasMany(Tenant::class,'id_usuario', 'id_usuario'); 
    }
    
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'id_rol', 'id_rol');
    }

}
