<?php

namespace App\Models\Erp;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use BelongsToTenant;

    protected $table = 'erp_facturas';

    protected $fillable = [
        'id_tenant',
        'id_pedido',
        'id_usuario',
        'tipo',
        'estado',
        'serie',
        'folio',
        'rfc_receptor',
        'razon_social_receptor',
        'uso_cfdi',
        'forma_pago_sat',
        'metodo_pago_sat',
        'subtotal',
        'iva',
        'total',
        'uuid',
        'xml_path',
        'pdf_path',
        'fecha_timbrado',
        'error_mensaje',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'iva' => 'float',
        'total' => 'float',
        'fecha_timbrado' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
