<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Tenant extends Model
    {
        use HasFactory, SoftDeletes;

        protected $primaryKey = 'id_tenant';
        
        protected $fillable = [
            'nombre_tenant',
            'subdominio',
            'estado',
            'id_tiponegocio',
            'id_plan',
            'moneda',
            'modulo_crm',
            'modulo_pos',
            'modulo_erp',
            'datos_nicho',
            'onboarding_completado',
        ];

        protected $casts = [
            'modulo_crm' => 'boolean',
            'modulo_pos' => 'boolean',
            'modulo_erp' => 'boolean',
            'datos_nicho' => 'array',
            'onboarding_completado' => 'boolean',
        ];

        public function negocio():BelongsTo
        {   
            return $this->belongsTo(Negocio::class, 'id_tiponegocio', 'id_tiponegocio');
        }
            public function plan():BelongsTo
        {   
            return $this->belongsTo(Plan::class, 'id_plan', 'id_plan');
        }

        public function categorias(){
            return $this->belongsTo(Categoria::class, 'id_tenant', 'id_tenant');            
        }
        public function productos(){
            return $this->hasMany(Producto::class, 'id_tenant', 'id_tenant');
        }
        public function usuarios(){
            return $this->hasMany(Usuarios::class, 'id_tenant', 'id_tenant');
        }
          public function clientes(){
            return $this->hasMany(Cliente::class, 'id_tenant', 'id_tenant');
        }
    }
