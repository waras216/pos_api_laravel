<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoItem;
use App\Models\Erp\Receta;
use App\Models\Producto;
use App\Services\Erp\AsientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RecetaController extends Controller
{
    public function __construct(private AsientoService $asientos) {}

    public function index(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $query = Receta::where('id_tenant', $idTenant)->with('producto')->latest('id');

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->query('id_cliente'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_cliente' => [
                'required',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
            'id_producto' => [
                'required',
                Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant),
            ],
            'dosis' => 'nullable|string|max:150',
            'cantidad' => 'required|integer|min:1',
        ]);

        $data['id_tenant'] = $idTenant;
        $data['pendiente'] = true;

        return response()->json(Receta::create($data)->load('producto'), 201);
    }

    public function dispensarLote(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => [Rule::exists('erp_recetas', 'id')->where('id_tenant', $idTenant)],
            'id_cliente' => [
                'required',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
        ]);

        $recetas = Receta::where('id_tenant', $idTenant)
            ->whereIn('id', $data['ids'])
            ->where('pendiente', true)
            ->get();

        if ($recetas->isEmpty()) {
            return response()->json(['message' => 'No hay recetas pendientes para dispensar'], 422);
        }

        $pedido = DB::transaction(function () use ($recetas, $data, $idTenant) {
            $productos = [];
            foreach ($recetas as $receta) {
                $producto = Producto::where('id_tenant', $idTenant)->findOrFail($receta->id_producto);
                if ($producto->stock < $receta->cantidad) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$producto->nombre} (disponible: {$producto->stock})",
                    ]);
                }
                $productos[$receta->id] = $producto;
            }

            $pedido = Pedido::create([
                'id_tenant' => $idTenant,
                'id_cliente' => $data['id_cliente'],
                'fecha' => now()->toDateString(),
                'estado' => 'facturado',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($recetas as $receta) {
                $producto = $productos[$receta->id];
                $subtotal = $receta->cantidad * $producto->precio;
                $total += $subtotal;

                PedidoItem::create([
                    'id_pedido' => $pedido->id,
                    'id_producto' => $producto->id_productos,
                    'cantidad' => $receta->cantidad,
                    'precio_unitario' => $producto->precio,
                    'costo_unitario' => $producto->precio_compra,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $receta->cantidad);

                MovimientoStock::create([
                    'id_tenant' => $idTenant,
                    'id_producto' => $producto->id_productos,
                    'tipo' => 'salida',
                    'cantidad' => $receta->cantidad,
                    'motivo' => 'dispensacion',
                    'referencia' => "receta:{$receta->id}·pedido:{$pedido->id}",
                    'stock_resultante' => $producto->stock,
                ]);

                $receta->update(['pendiente' => false]);
            }

            $pedido->update(['total' => $total]);

            return $pedido;
        });

        $this->asientos->registrarVenta($pedido->load('items'));

        return response()->json($pedido->load(['cliente', 'items.producto']), 201);
    }
}
