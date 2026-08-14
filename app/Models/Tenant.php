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
            'logo',
            'subdominio',
            'estado',
            'id_tiponegocio',
            'id_plan',
            'stripe_customer_id',
            'moneda',
            'sector',
            'idioma',
            'zona_horaria',
            'modulo_crm',
            'modulo_pos',
            'modulo_erp',
            'datos_nicho',
            'onboarding_completado',
            'rfc_emisor',
            'razon_social_emisor',
            'regimen_fiscal_emisor',
            'codigo_postal_emisor',
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

        public function suscripciones()
        {
            return $this->hasMany(Suscripcion::class, 'id_tenant', 'id_tenant');
        }

        public function suscripcionActual()
        {
            return $this->hasOne(Suscripcion::class, 'id_tenant', 'id_tenant')->latestOfMany('created_at');
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
        public function membresias(){
            return $this->hasMany(Membresia::class, 'id_tenant', 'id_tenant');
        }
        public function miembros(){
            return $this->belongsToMany(Usuarios::class, 'membresias', 'id_tenant', 'id_usuario')
                ->wherePivot('estado', 'activa');
        }
          public function clientes(){
            return $this->hasMany(Cliente::class, 'id_tenant', 'id_tenant');
        }
    }
