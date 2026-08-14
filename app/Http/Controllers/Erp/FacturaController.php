<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Factura;
use App\Models\Erp\Pedido;
use App\Services\Erp\FacturaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacturaController extends Controller
{
    public function __construct(private FacturaService $facturas) {}

    public function index(Request $request)
    {
        return response()->json(
            Factura::where('id_tenant', $request->user()->id_tenant)
                ->with(['pedido.cliente', 'usuario'])
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_pedido' => [
                'required',
                Rule::exists('erp_pedidos_venta', 'id')->where('id_tenant', $idTenant),
            ],
            'tipo' => ['required', Rule::in(['interna', 'timbrada'])],
            'rfc_receptor' => 'required|string|max:20',
            'razon_social_receptor' => 'required|string|max:150',
            'uso_cfdi' => 'nullable|string|max:10',
            'forma_pago_sat' => 'nullable|string|max:5',
            'metodo_pago_sat' => 'nullable|string|max:5',
            'serie' => 'nullable|string|max:10',
        ]);

        $pedido = Pedido::where('id_tenant', $idTenant)->findOrFail($data['id_pedido']);

        $factura = $this->facturas->crear($pedido, $data, $request->user()->id_usuario);

        return response()->json($factura->load(['pedido.cliente', 'usuario']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Factura::where('id_tenant', $request->user()->id_tenant)
                ->with(['pedido.cliente', 'pedido.items.producto', 'usuario'])
                ->findOrFail($id)
        );
    }

    public function timbrar(Request $request, string $id)
    {
        $factura = Factura::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $factura = $this->facturas->timbrar($factura);

        return response()->json($factura->load(['pedido.cliente', 'usuario']));
    }

    public function cancelar(Request $request, string $id)
    {
        $factura = Factura::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $factura = $this->facturas->cancelar($factura);

        return response()->json($factura->load(['pedido.cliente', 'usuario']));
    }
}
