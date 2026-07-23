<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\OrdenCompra;
use App\Models\Erp\OrdenCompraItem;
use App\Models\Producto;
use App\Services\Erp\AsientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrdenCompraController extends Controller
{
    public function __construct(private AsientoService $asientos) {}

    public function index(Request $request)
    {
        return response()->json(
            OrdenCompra::where('id_tenant', $request->user()->id_tenant)
                ->with(['proveedor', 'items.producto'])
                ->latest('fecha')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_proveedor' => [
                'required',
                Rule::exists('proveedores', 'id_proveedor')->where('id_tenant', $idTenant),
            ],
            'items' => 'required|array|min:1',
            'items.*.id_producto' => [
                'required',
                Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant),
            ],
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $orden = DB::transaction(function () use ($data, $idTenant) {
            $orden = OrdenCompra::create([
                'id_tenant' => $idTenant,
                'id_proveedor' => $data['id_proveedor'],
                'fecha' => now()->toDateString(),
                'estado' => 'pendiente',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;

                OrdenCompraItem::create([
                    'id_orden_compra' => $orden->id,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }

            $orden->update(['total' => $total]);

            return $orden;
        });

        return response()->json($orden->load(['proveedor', 'items.producto']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            OrdenCompra::where('id_tenant', $request->user()->id_tenant)
                ->with(['proveedor', 'items.producto'])
                ->findOrFail($id)
        );
    }

    public function destroy(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($orden->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden eliminar órdenes pendientes'], 422);
        }

        $orden->delete();

        return response()->json(['message' => 'Orden eliminada']);
    }

    public function recibir(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)
            ->with('items')
            ->findOrFail($id);

        if ($orden->estado !== 'pendiente') {
            return response()->json(['message' => 'La orden ya fue procesada'], 422);
        }

        DB::transaction(function () use ($orden, $request) {
            foreach ($orden->items as $item) {
                $producto = Producto::where('id_tenant', $request->user()->id_tenant)
                    ->findOrFail($item->id_producto);

                $producto->increment('stock', $item->cantidad);
                $producto->update(['precio_compra' => $item->precio_unitario]);

                MovimientoStock::create([
                    'id_tenant' => $request->user()->id_tenant,
                    'id_producto' => $producto->id_productos,
                    'tipo' => 'entrada',
                    'cantidad' => $item->cantidad,
                    'motivo' => 'compra',
                    'referencia' => "orden_compra:{$orden->id}",
                    'stock_resultante' => $producto->stock,
                ]);
            }

            $orden->update(['estado' => 'recibida']);
        });

        $this->asientos->registrarCompraRecibida($orden);

        return response()->json($orden->load(['proveedor', 'items.producto']));
    }

    public function cancelar(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($orden->estado !== 'pendiente') {
            return response()->json(['message' => 'La orden ya fue procesada'], 422);
        }

        $orden->update(['estado' => 'cancelada']);

        return response()->json($orden);
    }
}
