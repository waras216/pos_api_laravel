<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoItem;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuarios;
use App\Notifications\PedidoCanceladoNotification;
use App\Services\Erp\AsientoService;
use App\Services\Erp\PagoVentaService;
use App\Services\IntegracionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    public function __construct(private AsientoService $asientos, private PagoVentaService $pagos) {}

    public function index(Request $request)
    {
        return response()->json(
            Pedido::where('id_tenant', $request->user()->id_tenant)
                ->with(['cliente', 'items.producto', 'cajero', 'pagos'])
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $idUsuario = $request->user()->id_usuario;

        $data = $request->validate([
            'id_cliente' => [
                'required',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
            'items' => 'required|array|min:1',
            'items.*.id_producto' => [
                'required',
                Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant),
            ],
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'estado' => 'nullable|in:pendiente,enviado,facturado',
            'pagos' => 'required_if:estado,facturado|array',
            'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(PagoVentaService::METODOS)],
            'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
        ]);

        $pedido = DB::transaction(function () use ($data, $idTenant, $idUsuario) {
            $productos = [];
            foreach ($data['items'] as $item) {
                $producto = Producto::where('id_tenant', $idTenant)->findOrFail($item['id_producto']);

                if ($producto->stock < $item['cantidad']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$producto->nombre} (disponible: {$producto->stock})",
                    ]);
                }

                $productos[] = $producto;
            }

            $pedido = Pedido::create([
                'id_tenant' => $idTenant,
                'id_cliente' => $data['id_cliente'],
                'id_usuario' => $idUsuario,
                'fecha' => now()->toDateString(),
                'estado' => $data['estado'] ?? 'pendiente',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $i => $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;

                $producto = $productos[$i];

                PedidoItem::create([
                    'id_pedido' => $pedido->id,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'costo_unitario' => $producto->precio_compra,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $item['cantidad']);

                MovimientoStock::create([
                    'id_tenant' => $idTenant,
                    'id_producto' => $producto->id_productos,
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'venta',
                    'referencia' => "pedido:{$pedido->id}",
                    'stock_resultante' => $producto->stock,
                ]);
            }

            $pedido->update(['total' => $total]);

            if ($pedido->estado === 'facturado') {
                $this->pagos->validar($data['pagos'], $total);
                $this->pagos->crear($pedido, $data['pagos']);
            }

            return $pedido;
        });

        if ($pedido->estado === 'facturado') {
            $this->asientos->registrarVenta($pedido->load(['items', 'pagos']));
        }

        return response()->json($pedido->load(['cliente', 'items.producto', 'cajero', 'pagos']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Pedido::where('id_tenant', $request->user()->id_tenant)
                ->with(['cliente', 'items.producto', 'cajero', 'pagos'])
                ->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $pedido = Pedido::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($pedido->estado === 'cancelada') {
            return response()->json(['message' => 'El pedido está cancelado'], 422);
        }

        $vaAFacturarse = $pedido->estado !== 'facturado' && $request->input('estado') === 'facturado';

        $data = $request->validate([
            'estado' => 'required|in:pendiente,enviado,facturado',
            'pagos' => [Rule::requiredIf($vaAFacturarse), 'array'],
            'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(PagoVentaService::METODOS)],
            'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
        ]);

        $estadoAnterior = $pedido->estado;
        $pedido->update(['estado' => $data['estado']]);

        if ($pedido->estado === 'facturado' && $estadoAnterior !== 'facturado') {
            $this->pagos->validar($data['pagos'], $pedido->total);
            $this->pagos->crear($pedido, $data['pagos']);
            $this->asientos->registrarVenta($pedido->load(['items', 'pagos']));
        }

        return response()->json($pedido->load(['cliente', 'items.producto', 'cajero', 'pagos']));
    }

    public function destroy(Request $request, string $id)
    {
        $pedido = Pedido::where('id_tenant', $request->user()->id_tenant)
            ->with('items')
            ->findOrFail($id);

        if ($pedido->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden eliminar pedidos pendientes; usa cancelar'], 422);
        }

        DB::transaction(function () use ($pedido, $request) {
            $this->restaurarStock($pedido, $request->user()->id_tenant, 'eliminacion_venta');
            $pedido->delete();
        });

        return response()->json(['message' => 'Pedido eliminado']);
    }

    public function cancelar(Request $request, string $id)
    {
        $pedido = Pedido::where('id_tenant', $request->user()->id_tenant)
            ->with('items')
            ->findOrFail($id);

        if (in_array($pedido->estado, ['cancelada', 'facturado'])) {
            return response()->json(['message' => 'El pedido no se puede cancelar'], 422);
        }

        DB::transaction(function () use ($pedido, $request) {
            $this->restaurarStock($pedido, $request->user()->id_tenant, 'cancelacion_venta');
            $pedido->update(['estado' => 'cancelada']);
        });

        if (IntegracionService::conectada($pedido->id_tenant, 'email')) {
            $idsAdmin = Rol::idsAdminTenant($pedido->id_tenant);
            foreach (Usuarios::whereIn('id_usuario', $idsAdmin)->whereNotNull('email')->get() as $admin) {
                $admin->notify(new PedidoCanceladoNotification($pedido));
            }
        }

        return response()->json($pedido->load(['cliente', 'items.producto', 'cajero', 'pagos']));
    }

    private function restaurarStock(Pedido $pedido, int $idTenant, string $motivo): void
    {
        foreach ($pedido->items as $item) {
            $producto = Producto::where('id_tenant', $idTenant)->findOrFail($item->id_producto);

            $producto->increment('stock', $item->cantidad);

            MovimientoStock::create([
                'id_tenant' => $idTenant,
                'id_producto' => $producto->id_productos,
                'tipo' => 'entrada',
                'cantidad' => $item->cantidad,
                'motivo' => $motivo,
                'referencia' => "pedido:{$pedido->id}",
                'stock_resultante' => $producto->stock,
            ]);
        }
    }
}
