<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable  
{
    use HasApiTokens;

    protected $table      = 'usuario'; 
    protected $primaryKey = 'id_usr';

    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
