<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class Tenant extends Model
    {
        use HasFactory;

        protected $primaryKey = 'id_tenant';
        
        protected $fillable = [
            'nombre_tenant',
            'subdominio',
            'estado',
            'id_tiponegocio',
            'id_plan',
        ];

        public function negocio():BelongsTo
        {   
            return $this->belongsTo(Negocio::class, 'id_tiponegocio', 'id_tiponegocio');
        }
            public function plan():BelongsTo
        {   
            return $this->belongsTo(Plan::class, 'id_plan', 'id_plan');
        }
        public function usuario():BelongsTo{
            return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
        }
    }
