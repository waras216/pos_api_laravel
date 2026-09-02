<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Estadia;
use App\Models\Erp\Habitacion;
use App\Models\Erp\HabitacionConsumo;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoItem;
use App\Models\Erp\Reserva;
use App\Models\Producto;
use App\Services\Erp\AsientoService;
use App\Services\Erp\PagoVentaService;
use Carbon\Carbon;
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
            'precio' => 'required|numeric|min:0',
            'piso' => 'sometimes|integer|min:1',
        ]);

        $data['id_tenant'] = $idTenant;

        $habitacion = Habitacion::create($data);

        // create() solo trae los atributos que se enviaron: 'estado', 'tipo' y
        // 'piso' tienen default a nivel de columna (ver migración) que no queda
        // reflejado en el objeto en memoria sin un refresh — y el frontend
        // asume 'consumos' siempre es un array (aunque venga vacío en un
        // registro nuevo), no undefined.
        return response()->json($this->conRelaciones($habitacion->refresh()), 201);
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'numero' => [
                'sometimes', 'integer', 'min:1',
                Rule::unique('erp_habitaciones', 'numero')->where('id_tenant', $idTenant)->ignore($habitacion->id),
            ],
            'tipo' => 'sometimes|string|max:20',
            'precio' => 'sometimes|numeric|min:0',
            'piso' => 'sometimes|integer|min:1',
        ]);

        $habitacion->update($data);

        return response()->json($this->conRelaciones($habitacion));
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

    public function papelera(Request $request)
    {
        return response()->json(
            Habitacion::onlyTrashed()
                ->where('id_tenant', $request->user()->id_tenant)
                ->latest('deleted_at')
                ->get()
        );
    }

    public function restaurar(Request $request, string $id)
    {
        $habitacion = Habitacion::onlyTrashed()->where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $habitacion->restore();

        return response()->json($this->conRelaciones($habitacion));
    }

    public function checkIn(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        if ($habitacion->estado !== 'libre') {
            return response()->json(['message' => 'La habitación no está libre'], 422);
        }

        $data = $request->validate([
            'huesped' => 'required|string|max:150',
            'noches' => 'required|integer|min:1',
        ]);

        $checkIn = now()->toDateString();
        $checkOut = now()->addDays($data['noches'])->toDateString();

        $habitacion->update([
            'estado' => 'ocupada',
            'huesped' => $data['huesped'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'noches' => $data['noches'],
        ]);

        Estadia::create([
            'id_tenant' => $idTenant,
            'id_habitacion' => $habitacion->id,
            'huesped' => $data['huesped'],
            'check_in' => $checkIn,
            'check_out_programado' => $checkOut,
            'noches' => $data['noches'],
            'estado' => 'activa',
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

        $producto = Producto::where('id_tenant', $idTenant)->with('categoria')->findOrFail($data['id_producto']);
        $cantidad = $data['cantidad'] ?? 1;

        $consumo = $habitacion->consumos()->where('id_producto', $producto->id_productos)->first();
        if ($consumo) {
            $consumo->increment('cantidad', $cantidad);
        } else {
            HabitacionConsumo::create([
                'id_habitacion' => $habitacion->id,
                'id_producto' => $producto->id_productos,
                'nombre' => $producto->nombre,
                'seccion' => $producto->categoria?->nombre,
                'precio_unitario' => $producto->precio,
                'cantidad' => $cantidad,
            ]);
        }

        return response()->json($this->conRelaciones($habitacion));
    }

    public function quitarConsumo(Request $request, string $id, string $consumoId)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);
        $consumo = $habitacion->consumos()->where('id', $consumoId)->first();

        if ($consumo) {
            $cantidad = max(1, (int) $request->input('cantidad', 1));
            if ($cantidad >= $consumo->cantidad) {
                $consumo->delete();
            } else {
                $consumo->decrement('cantidad', $cantidad);
            }
        }

        return response()->json($this->conRelaciones($habitacion));
    }

    public function marcarSalida(Request $request, string $id)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'estado' => 'required|in:checkout,ocupada',
        ]);

        if (! in_array($habitacion->estado, ['ocupada', 'checkout'])) {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        $habitacion->update(['estado' => $data['estado']]);

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
        $idUsuario = $request->user()->id_usuario;
        $habitacion = $this->habitacionDelTenant($request, $id);

        if (! in_array($habitacion->estado, ['ocupada', 'checkout'])) {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        $consumos = $habitacion->consumos()->get();
        $cargoHospedaje = (float) ($habitacion->precio ?? 0) * (float) ($habitacion->noches ?? 0);

        $pedido = null;

        if ($consumos->isNotEmpty() || $cargoHospedaje > 0) {
            $data = $request->validate([
                'id_cliente' => [
                    'required',
                    Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
                ],
                'pagos' => 'required|array',
                'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(PagoVentaService::METODOS)],
                'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
            ]);

            $pedido = DB::transaction(function () use ($consumos, $cargoHospedaje, $data, $idTenant, $idUsuario, $habitacion) {
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
                    'id_usuario' => $idUsuario,
                    'fecha' => now()->toDateString(),
                    'estado' => 'facturado',
                    'total' => 0,
                ]);

                $total = 0;

                if ($cargoHospedaje > 0) {
                    $total += $cargoHospedaje;

                    PedidoItem::create([
                        'id_pedido' => $pedido->id,
                        'id_producto' => null,
                        'descripcion' => "Hospedaje ({$habitacion->noches} noche(s), hab. {$habitacion->numero})",
                        'seccion' => 'Hospedaje',
                        'cantidad' => 1,
                        'precio_unitario' => $cargoHospedaje,
                        'costo_unitario' => 0,
                        'subtotal' => $cargoHospedaje,
                    ]);
                }

                foreach ($consumos as $item) {
                    $subtotal = $item->cantidad * $item->precio_unitario;
                    $total += $subtotal;
                    $producto = $item->id_producto ? ($productos[$item->id_producto] ?? null) : null;

                    PedidoItem::create([
                        'id_pedido' => $pedido->id,
                        'id_producto' => $item->id_producto,
                        'descripcion' => $item->id_producto ? null : $item->nombre,
                        'seccion' => $item->seccion,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio_unitario,
                        'costo_unitario' => $producto?->precio_compra ?? 0,
                        'subtotal' => $subtotal,
                    ]);

                    if ($producto) {
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

        $estadia = Estadia::where('id_tenant', $idTenant)
            ->where('id_habitacion', $habitacion->id)
            ->where('estado', 'activa')
            ->latest('check_in')
            ->first();

        if ($estadia) {
            $totalConsumos = $consumos->sum(fn ($c) => $c->cantidad * $c->precio_unitario);

            $estadia->update([
                'check_out_real' => now(),
                'total_hospedaje' => $cargoHospedaje,
                'total_consumos' => $totalConsumos,
                'total' => $cargoHospedaje + $totalConsumos,
                'id_cliente' => $pedido?->id_cliente,
                'id_pedido' => $pedido?->id,
                'estado' => 'finalizada',
            ]);
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
            'pedido' => $pedido?->load(['cliente', 'items.producto', 'cajero', 'pagos']),
        ]);
    }

    public function historial(Request $request)
    {
        $estadias = Estadia::where('id_tenant', $request->user()->id_tenant)
            ->with(['habitacion:id,numero,tipo', 'cliente:id_cliente,nombre'])
            ->orderByDesc('check_in')
            ->limit(200)
            ->get();

        return response()->json($estadias);
    }

    public function disponibilidad(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = isset($data['desde']) ? Carbon::parse($data['desde'])->startOfDay() : now()->startOfDay();
        $hasta = isset($data['hasta']) ? Carbon::parse($data['hasta'])->startOfDay() : $desde->copy()->addDays(13);

        $dias = [];
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $dias[] = $d->toDateString();
        }

        $habitaciones = Habitacion::where('id_tenant', $idTenant)->orderBy('numero')->get();

        $reservasPorHabitacion = Reserva::where('id_tenant', $idTenant)
            ->where('estado', 'pendiente')
            ->where('fecha_checkin', '<=', $hasta->toDateString())
            ->where('fecha_checkout', '>', $desde->toDateString())
            ->get()
            ->groupBy('id_habitacion');

        $resultado = $habitaciones->map(function (Habitacion $habitacion) use ($dias, $reservasPorHabitacion) {
            $reservas = $reservasPorHabitacion->get($habitacion->id, collect());
            $estadosPorDia = [];

            foreach ($dias as $dia) {
                if ($habitacion->estado === 'mantenimiento') {
                    $estadosPorDia[$dia] = 'mantenimiento';
                    continue;
                }

                $enEstanciaActiva = in_array($habitacion->estado, ['ocupada', 'checkout'])
                    && $habitacion->check_in && $habitacion->check_out
                    && $dia >= $habitacion->check_in->toDateString()
                    && $dia < $habitacion->check_out->toDateString();

                if ($enEstanciaActiva) {
                    $estadosPorDia[$dia] = 'ocupada';
                    continue;
                }

                $enReserva = $reservas->first(fn (Reserva $r) => $dia >= $r->fecha_checkin->toDateString() && $dia < $r->fecha_checkout->toDateString());
                $estadosPorDia[$dia] = $enReserva ? 'reservada' : 'libre';
            }

            return [
                'id' => $habitacion->id,
                'numero' => $habitacion->numero,
                'tipo' => $habitacion->tipo,
                'dias' => $estadosPorDia,
            ];
        });

        return response()->json([
            'dias' => $dias,
            'habitaciones' => $resultado,
        ]);
    }
}
