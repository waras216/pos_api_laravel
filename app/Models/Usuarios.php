<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Usuarios extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class, 'id_tenant', 'id_tenant');
    }
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'id_rol', 'id_rol');
    }

}
