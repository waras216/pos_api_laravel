<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Comanda;
use App\Models\Erp\ComandaItem;
use App\Models\Erp\Habitacion;
use App\Models\Erp\HabitacionConsumo;
use App\Models\Erp\Mesa;
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

class MesaController extends Controller
{
    public function __construct(private AsientoService $asientos, private PagoVentaService $pagos) {}

    private function mesaDelTenant(Request $request, string $id): Mesa
    {
        return Mesa::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
    }

    private function conRelaciones(Mesa $mesa): Mesa
    {
        return $mesa->load('comandaActiva.items.producto');
    }

    public function index(Request $request)
    {
        $mesas = Mesa::where('id_tenant', $request->user()->id_tenant)
            ->with('comandaActiva.items.producto')
            ->orderBy('numero')
            ->get();

        return response()->json($mesas);
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'numero' => [
                'required', 'integer', 'min:1',
                Rule::unique('erp_mesas', 'numero')->where('id_tenant', $idTenant),
            ],
            'capacidad' => 'sometimes|integer|min:1',
        ]);

        $data['id_tenant'] = $idTenant;

        $mesa = Mesa::create($data);

        // Igual que en HabitacionController::store: 'estado' tiene default a
        // nivel de columna, así que sin refresh el objeto en memoria no lo trae.
        return response()->json($this->conRelaciones($mesa->refresh()), 201);
    }

    public function destroy(Request $request, string $id)
    {
        $mesa = $this->mesaDelTenant($request, $id);

        if ($mesa->estado !== 'libre') {
            return response()->json(['message' => 'Solo se pueden eliminar mesas libres'], 422);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada']);
    }

    public function abrir(Request $request, string $id)
    {
        $mesa = $this->mesaDelTenant($request, $id);

        $data = $request->validate([
            'mesero' => 'nullable|string|max:150',
        ]);

        $mesa->update([
            'estado' => 'ocupada',
            'mesero' => $data['mesero'] ?? $request->user()->nombre,
        ]);

        if (! $mesa->comandaActiva) {
            Comanda::create([
                'id_tenant' => $request->user()->id_tenant,
                'id_mesa' => $mesa->id,
                'estado' => 'abierta',
            ]);
        }

        return response()->json($this->conRelaciones($mesa));
    }

    public function pedirCuenta(Request $request, string $id)
    {
        $mesa = $this->mesaDelTenant($request, $id);
        $mesa->update(['estado' => 'cuenta']);

        return response()->json($this->conRelaciones($mesa));
    }

    public function agregarItem(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $mesa = $this->mesaDelTenant($request, $id);

        $data = $request->validate([
            'id_producto' => [
                'required',
                Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant),
            ],
            'cantidad' => 'sometimes|integer|min:1',
        ]);

        $comanda = $mesa->comandaActiva ?: Comanda::create([
            'id_tenant' => $idTenant,
            'id_mesa' => $mesa->id,
            'estado' => 'abierta',
        ]);

        $producto = Producto::where('id_tenant', $idTenant)->findOrFail($data['id_producto']);
        $cantidad = $data['cantidad'] ?? 1;

        $item = $comanda->items()->where('id_producto', $producto->id_productos)->first();
        if ($item) {
            $item->increment('cantidad', $cantidad);
        } else {
            ComandaItem::create([
                'id_comanda' => $comanda->id,
                'id_producto' => $producto->id_productos,
                'nombre' => $producto->nombre,
                'precio_unitario' => $producto->precio,
                'cantidad' => $cantidad,
            ]);
        }

        $this->recalcularTotal($comanda);

        return response()->json($this->conRelaciones($mesa));
    }

    public function actualizarItem(Request $request, string $id, string $itemId)
    {
        $mesa = $this->mesaDelTenant($request, $id);
        $comanda = $mesa->comandaActiva;
        abort_if(! $comanda, 404);

        $data = $request->validate([
            'cantidad' => 'required|integer',
        ]);

        $item = $comanda->items()->findOrFail($itemId);

        if ($data['cantidad'] <= 0) {
            $item->delete();
        } else {
            $item->update(['cantidad' => $data['cantidad']]);
        }

        $this->recalcularTotal($comanda);

        return response()->json($this->conRelaciones($mesa));
    }

    public function quitarItem(Request $request, string $id, string $itemId)
    {
        $mesa = $this->mesaDelTenant($request, $id);
        $comanda = $mesa->comandaActiva;
        abort_if(! $comanda, 404);

        $comanda->items()->where('id', $itemId)->delete();
        $this->recalcularTotal($comanda);

        return response()->json($this->conRelaciones($mesa));
    }

    public function enviarCocina(Request $request, string $id)
    {
        $mesa = $this->mesaDelTenant($request, $id);
        $comanda = $mesa->comandaActiva;
        abort_if(! $comanda, 422, 'La mesa no tiene una comanda activa');

        $comanda->update(['estado' => 'enviada', 'enviada_cocina' => true]);

        return response()->json($this->conRelaciones($mesa));
    }

    public function cobrar(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $mesa = $this->mesaDelTenant($request, $id);
        $comanda = $mesa->comandaActiva()->with('items.producto.categoria')->first();

        if (! $comanda || $comanda->items->isEmpty()) {
            return response()->json(['message' => 'La mesa no tiene items para cobrar'], 422);
        }

        $data = $request->validate([
            'id_cliente' => [
                'required',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
            'pagos' => 'present|array',
            'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(PagoVentaService::METODOS)],
            'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
        ]);

        $pedido = DB::transaction(function () use ($comanda, $data, $idTenant, $mesa) {
            $productos = [];
            foreach ($comanda->items as $item) {
                if (! $item->id_producto) {
                    continue;
                }
                $producto = Producto::where('id_tenant', $idTenant)->findOrFail($item->id_producto);
                if ($producto->controla_stock && $producto->stock < $item->cantidad) {
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
                'canal' => 'comedor',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($comanda->items as $item) {
                $subtotal = $item->cantidad * $item->precio_unitario;
                $total += $subtotal;

                PedidoItem::create([
                    'id_pedido' => $pedido->id,
                    'id_producto' => $item->id_producto,
                    'seccion' => $item->producto?->categoria?->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'costo_unitario' => $item->id_producto ? ($productos[$item->id_producto]->precio_compra ?? 0) : 0,
                    'subtotal' => $subtotal,
                ]);

                if ($item->id_producto && isset($productos[$item->id_producto]) && $productos[$item->id_producto]->controla_stock) {
                    $producto = $productos[$item->id_producto];
                    $producto->decrement('stock', $item->cantidad);

                    MovimientoStock::create([
                        'id_tenant' => $idTenant,
                        'id_producto' => $producto->id_productos,
                        'tipo' => 'salida',
                        'cantidad' => $item->cantidad,
                        'motivo' => 'venta_mesa',
                        'referencia' => "mesa:{$mesa->numero}·pedido:{$pedido->id}",
                        'stock_resultante' => $producto->stock,
                    ]);
                }
            }

            $pedido->update(['total' => $total]);

            $this->pagos->validar($data['pagos'], $total);
            $this->pagos->crear($pedido, $data['pagos']);

            $comanda->update(['estado' => 'cerrada']);
            $mesa->update(['estado' => 'libre', 'mesero' => null]);

            return $pedido;
        });

        $this->asientos->registrarVenta($pedido->load(['items', 'pagos']));

        return response()->json([
            'mesa' => $this->conRelaciones($mesa->fresh()),
            'pedido' => $pedido->load(['cliente', 'items.producto']),
        ]);
    }

    /**
     * Transfiere la comanda activa de la mesa a la cuenta de una habitación ocupada, en vez de
     * cobrarla directamente — para el restaurante de un hotel, donde el comensal suele ser un
     * huésped que paga todo junto al hacer check-out (ver [[HabitacionController::checkOut]]).
     * No descuenta stock ni genera Pedido/pago aquí: se comporta igual que un consumo de Room
     * Service agregado uno por uno, y sigue el mismo ciclo de vida que esos (se liquida en el
     * checkout de la habitación).
     */
    public function cargarHabitacion(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $mesa = $this->mesaDelTenant($request, $id);
        $comanda = $mesa->comandaActiva()->with('items.producto.categoria')->first();

        if (! $comanda || $comanda->items->isEmpty()) {
            return response()->json(['message' => 'La mesa no tiene items para cargar'], 422);
        }

        $data = $request->validate([
            'id_habitacion' => [
                'required',
                Rule::exists('erp_habitaciones', 'id')->where('id_tenant', $idTenant),
            ],
        ]);

        $habitacion = Habitacion::where('id_tenant', $idTenant)->findOrFail($data['id_habitacion']);
        if ($habitacion->estado !== 'ocupada') {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        DB::transaction(function () use ($comanda, $habitacion, $mesa) {
            foreach ($comanda->items as $item) {
                $consumo = $item->id_producto
                    ? $habitacion->consumos()->where('id_producto', $item->id_producto)->first()
                    : null;

                if ($consumo) {
                    $consumo->increment('cantidad', $item->cantidad);
                } else {
                    HabitacionConsumo::create([
                        'id_habitacion' => $habitacion->id,
                        'id_producto' => $item->id_producto,
                        'nombre' => $item->nombre,
                        'seccion' => $item->producto?->categoria?->nombre ?? 'Restaurante',
                        'precio_unitario' => $item->precio_unitario,
                        'cantidad' => $item->cantidad,
                    ]);
                }
            }

            $comanda->update(['estado' => 'cerrada']);
            $mesa->update(['estado' => 'libre', 'mesero' => null]);
        });

        return response()->json([
            'mesa' => $this->conRelaciones($mesa->fresh()),
            'habitacion' => $habitacion->fresh()->load(['consumos.producto', 'estadiaActiva']),
        ]);
    }

    private function recalcularTotal(Comanda $comanda): void
    {
        $total = $comanda->items()->get()->sum(fn ($item) => $item->cantidad * $item->precio_unitario);
        $comanda->update(['total' => $total]);
    }
}
