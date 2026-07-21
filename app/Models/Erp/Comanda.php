<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_comandas';

    protected $fillable = [
        'id_tenant',
        'id_mesa',
        'estado',
        'enviada_cocina',
        'total',
    ];

    protected $casts = [
        'enviada_cocina' => 'boolean',
        'total' => 'float',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa');
    }

    public function items()
    {
        return $this->hasMany(ComandaItem::class, 'id_comanda');
    }
}
