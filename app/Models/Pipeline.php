<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pipeline extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'pipelines'; 
    protected $primaryKey = 'id_pipeline';

    protected $fillable = [
        'id_tenant',
        'nombre',
        'activo',
    ];

    public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class, 'id_pipeline', 'id_pipeline');
    }    
}
