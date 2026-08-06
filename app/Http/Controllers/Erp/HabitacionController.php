<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Habitacion;
use App\Models\Erp\HabitacionConsumo;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoItem;
use App\Models\Producto;
use App\Services\Erp\AsientoService;
use App\Services\Erp\PagoVentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HabitacionController extends Controller
{
    public function __construct(private AsientoService $asientos, private PagoVentaService $pagos) {}

    private function habitacionDelTenant(Request $request, string $id): Habitacion
    {
        return Habitacion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
    }

    private function conRelaciones(Habitacion $habitacion): Habitacion
    {
        return $habitacion->load('consumos.producto');
    }

    public function index(Request $request)
    {
        $habitaciones = Habitacion::where('id_tenant', $request->user()->id_tenant)
            ->with('consumos.producto')
            ->orderBy('numero')
            ->get();

        return response()->json($habitaciones);
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'numero' => [
                'required', 'integer', 'min:1',
                Rule::unique('erp_habitaciones', 'numero')->where('id_tenant', $idTenant),
            ],
            'tipo' => 'sometimes|string|max:20',
            'piso' => 'sometimes|integer|min:1',
        ]);

        $data['id_tenant'] = $idTenant;

        return response()->json(Habitacion::create($data), 201);
    }

    public function destroy(Request $request, string $id)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);

        if ($habitacion->estado !== 'libre') {
            return response()->json(['message' => 'Solo se pueden eliminar habitaciones libres'], 422);
        }

        $habitacion->delete();

        return response()->json(['message' => 'Habitación eliminada']);
    }

    public function checkIn(Request $request, string $id)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);

        if ($habitacion->estado !== 'libre') {
            return response()->json(['message' => 'La habitación no está libre'], 422);
        }

        $data = $request->validate([
            'huesped' => 'required|string|max:150',
            'noches' => 'required|integer|min:1',
        ]);

        $checkIn = now()->toDateString();

        $habitacion->update([
            'estado' => 'ocupada',
            'huesped' => $data['huesped'],
            'check_in' => $checkIn,
            'check_out' => now()->addDays($data['noches'])->toDateString(),
            'noches' => $data['noches'],
        ]);

        return response()->json($this->conRelaciones($habitacion));
    }

    public function agregarConsumo(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        if ($habitacion->estado !== 'ocupada') {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        $data = $request->validate([
            'id_producto' => [
                'required',
                Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant),
            ],
            'cantidad' => 'sometimes|integer|min:1',
        ]);

        $producto = Producto::where('id_tenant', $idTenant)->findOrFail($data['id_producto']);
        $cantidad = $data['cantidad'] ?? 1;

        $consumo = $habitacion->consumos()->where('id_producto', $producto->id_productos)->first();
        if ($consumo) {
            $consumo->increment('cantidad', $cantidad);
        } else {
            HabitacionConsumo::create([
                'id_habitacion' => $habitacion->id,
                'id_producto' => $producto->id_productos,
                'nombre' => $producto->nombre,
                'precio_unitario' => $producto->precio,
                'cantidad' => $cantidad,
            ]);
        }

        return response()->json($this->conRelaciones($habitacion));
    }

    public function quitarConsumo(Request $request, string $id, string $consumoId)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);
        $habitacion->consumos()->where('id', $consumoId)->delete();

        return response()->json($this->conRelaciones($habitacion));
    }

    public function mantenimiento(Request $request, string $id)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'estado' => 'required|in:mantenimiento,libre',
        ]);

        if (! in_array($habitacion->estado, ['libre', 'mantenimiento'])) {
            return response()->json(['message' => 'La habitación tiene una estancia activa'], 422);
        }

        $habitacion->update(['estado' => $data['estado']]);

        return response()->json($this->conRelaciones($habitacion));
    }

    public function checkOut(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        if (! in_array($habitacion->estado, ['ocupada', 'checkout'])) {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        $consumos = $habitacion->consumos()->get();

        $pedido = null;

        if ($consumos->isNotEmpty()) {
            $data = $request->validate([
                'id_cliente' => [
                    'required',
                    Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
                ],
                'pagos' => 'required|array',
                'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(PagoVentaService::METODOS)],
                'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
            ]);

            $pedido = DB::transaction(function () use ($consumos, $data, $idTenant, $habitacion) {
                $productos = [];
                foreach ($consumos as $item) {
                    if (! $item->id_producto) {
                        continue;
                    }
                    $producto = Producto::where('id_tenant', $idTenant)->findOrFail($item->id_producto);
                    if ($producto->stock < $item->cantidad) {
                        throw ValidationException::withMessages([
                            'items' => "Stock insuficiente para {$producto->nombre} (disponible: {$producto->stock})",
                        ]);
                    }
                    $productos[$item->id_producto] = $producto;
                }

                $pedido = Pedido::create([
                    'id_tenant' => $idTenant,
                    'id_cliente' => $data['id_cliente'],
                    'fecha' => now()->toDateString(),
                    'estado' => 'facturado',
                    'total' => 0,
                ]);

                $total = 0;
                foreach ($consumos as $item) {
                    $subtotal = $item->cantidad * $item->precio_unitario;
                    $total += $subtotal;

                    PedidoItem::create([
                        'id_pedido' => $pedido->id,
                        'id_producto' => $item->id_producto,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio_unitario,
                        'costo_unitario' => $producto->precio_compra,
                        'subtotal' => $subtotal,
                    ]);

                    if ($item->id_producto && isset($productos[$item->id_producto])) {
                        $producto = $productos[$item->id_producto];
                        $producto->decrement('stock', $item->cantidad);

                        MovimientoStock::create([
                            'id_tenant' => $idTenant,
                            'id_producto' => $producto->id_productos,
                            'tipo' => 'salida',
                            'cantidad' => $item->cantidad,
                            'motivo' => 'consumo_habitacion',
                            'referencia' => "habitacion:{$habitacion->numero}·pedido:{$pedido->id}",
                            'stock_resultante' => $producto->stock,
                        ]);
                    }
                }

                $pedido->update(['total' => $total]);

                $this->pagos->validar($data['pagos'], $total);
                $this->pagos->crear($pedido, $data['pagos']);

                return $pedido;
            });
        }

        $habitacion->consumos()->delete();
        $habitacion->update([
            'estado' => 'libre',
            'huesped' => null,
            'check_in' => null,
            'check_out' => null,
            'noches' => null,
        ]);

        if ($pedido) {
            $this->asientos->registrarVenta($pedido->load(['items', 'pagos']));
        }

        return response()->json([
            'habitacion' => $this->conRelaciones($habitacion->fresh()),
            'pedido' => $pedido?->load(['cliente', 'items.producto']),
        ]);
    }
}
