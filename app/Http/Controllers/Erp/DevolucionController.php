<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Devolucion;
use App\Models\Erp\MovimientoStock;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Devolucion::where('id_tenant', $request->user()->id_tenant)
                ->with(['pedido.cliente', 'producto', 'usuario'])
                ->latest('fecha')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_pedido' => ['required', Rule::exists('erp_pedidos_venta', 'id')->where('id_tenant', $idTenant)],
            'id_producto' => ['required', Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant)],
            'cantidad' => 'required|integer|min:1',
            'tipo' => ['required', Rule::in(['devolucion', 'garantia'])],
            'motivo' => 'nullable|string|max:250',
        ]);

        $data['id_tenant'] = $idTenant;
        $data['id_usuario'] = $request->user()->id_usuario;
        $data['estado'] = 'pendiente';
        $data['fecha'] = now()->toDateString();

        return response()->json(Devolucion::create($data)->load(['pedido.cliente', 'producto', 'usuario']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Devolucion::where('id_tenant', $request->user()->id_tenant)->with(['pedido.cliente', 'producto', 'usuario'])->findOrFail($id)
        );
    }

    public function aprobar(Request $request, string $id)
    {
        $devolucion = Devolucion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($devolucion->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden aprobar devoluciones pendientes.'], 422);
        }

        $data = $request->validate(['monto_reembolso' => 'nullable|numeric|min:0']);

        $devolucion->update(['estado' => 'aprobada', 'monto_reembolso' => $data['monto_reembolso'] ?? $devolucion->monto_reembolso]);

        return response()->json($devolucion->load(['pedido.cliente', 'producto', 'usuario']));
    }

    public function rechazar(Request $request, string $id)
    {
        $devolucion = Devolucion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($devolucion->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden rechazar devoluciones pendientes.'], 422);
        }

        $devolucion->update(['estado' => 'rechazada']);

        return response()->json($devolucion->load(['pedido.cliente', 'producto', 'usuario']));
    }

    // Regresa el producto al stock (si aplica a inventario) y marca la
    // devolución como completada -- separado de aprobar() porque aprobar es
    // una decisión administrativa y completar es el movimiento físico real
    // (el producto puede tardar días en regresar a la sucursal).
    public function completar(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $devolucion = Devolucion::where('id_tenant', $idTenant)->findOrFail($id);

        if ($devolucion->estado !== 'aprobada') {
            return response()->json(['message' => 'Solo se pueden completar devoluciones aprobadas.'], 422);
        }

        DB::transaction(function () use ($devolucion, $idTenant) {
            $producto = Producto::where('id_tenant', $idTenant)->find($devolucion->id_producto);

            if ($producto && $producto->controla_stock) {
                $producto->increment('stock', $devolucion->cantidad);

                MovimientoStock::create([
                    'id_tenant' => $idTenant,
                    'id_producto' => $producto->id_productos,
                    'tipo' => 'entrada',
                    'cantidad' => $devolucion->cantidad,
                    'motivo' => $devolucion->tipo === 'garantia' ? 'garantia' : 'devolucion',
                    'referencia' => "devolucion:{$devolucion->id}",
                    'stock_resultante' => $producto->stock,
                ]);
            }

            $devolucion->update(['estado' => 'completada']);
        });

        return response()->json($devolucion->load(['pedido.cliente', 'producto', 'usuario']));
    }

    public function destroy(Request $request, string $id)
    {
        $devolucion = Devolucion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($devolucion->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden eliminar devoluciones pendientes.'], 422);
        }

        $devolucion->delete();

        return response()->json(['message' => 'Devolución eliminada']);
    }
}
