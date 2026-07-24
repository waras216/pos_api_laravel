<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $primaryKey = 'id_suscripcion';

    protected $fillable = [
        'id_tenant',
        'id_plan',
        'stripe_subscription_id',
        'stripe_price_id',
        'estado',
        'fecha_inicio',
        'fecha_fin_periodo_actual',
        'cancela_al_final_periodo',
        'fecha_cancelacion',
        'ultimo_evento_stripe',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin_periodo_actual' => 'datetime',
        'cancela_al_final_periodo' => 'boolean',
        'fecha_cancelacion' => 'datetime',
        'ultimo_evento_stripe' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id_plan');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['activa', 'periodo_gracia']);
    }

    public static function tenantTieneAccesoActivo(int $idTenant): bool
    {
        return static::where('id_tenant', $idTenant)
            ->activas()
            ->exists();
    }
}
