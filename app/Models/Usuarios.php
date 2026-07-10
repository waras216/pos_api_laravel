<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Usuarios extends Authenticatable
{
    // Sanctum sigue siendo la autenticación real de las rutas de negocio
    // (auth:sanctum). Passport (auth:api-oauth, Fase 3) convive detrás del
    // mismo trait: Sanctum::withAccessToken()/currentAccessToken() no
    // validan de qué guard viene el token, solo lo guardan -- eso es lo
    // único que el TokenGuard de Passport necesita del modelo. Agregar
    // Passport\HasApiTokens acá es imposible: ambos traits declaran la
    // propiedad $accessToken con firmas distintas, y PHP no permite
    // resolver choques de propiedades entre traits (solo de métodos).
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
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }
        public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class, 'id_usuario', 'id_usuario');
    }

}
