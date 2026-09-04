<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Estadia;
use App\Models\Erp\Habitacion;
use App\Models\Erp\HabitacionConsumo;
use App\Models\Erp\HabitacionIncidencia;
use App\Models\Erp\MovimientoStock;
use App\Models\Erp\Pedido;
use App\Models\Erp\PedidoItem;
use App\Models\Erp\Reserva;
use App\Models\Producto;
use App\Services\Erp\AsientoService;
use App\Services\Erp\PagoVentaService;
use App\Services\Erp\TarifaTemporadaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HabitacionController extends Controller
{
    public function __construct(
        private AsientoService $asientos,
        private PagoVentaService $pagos,
        private TarifaTemporadaService $tarifas,
    ) {}

    private function habitacionDelTenant(Request $request, string $id): Habitacion
    {
        return Habitacion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
    }

    private function conRelaciones(Habitacion $habitacion): Habitacion
    {
        return $habitacion->load(['consumos.producto', 'estadiaActiva']);
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

        if ($habitacion->estado_limpieza !== 'limpia') {
            return response()->json(['message' => 'La habitación necesita limpieza antes del check-in'], 422);
        }

        $data = $request->validate([
            'huesped' => 'required|string|max:150',
            'noches' => 'required|integer|min:1',
            'id_cliente' => [
                'nullable',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
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
            'id_cliente' => $data['id_cliente'] ?? null,
            'check_in' => $checkIn,
            'check_out_programado' => $checkOut,
            'noches' => $data['noches'],
            'estado' => 'activa',
        ]);

        return response()->json($this->conRelaciones($habitacion));
    }

    public function registrarDocumento(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        $estadia = Estadia::where('id_tenant', $idTenant)
            ->where('id_habitacion', $habitacion->id)
            ->where('estado', 'activa')
            ->latest('check_in')
            ->first();

        if (! $estadia) {
            return response()->json(['message' => 'La habitación no tiene una estancia activa'], 422);
        }

        $data = $request->validate([
            'documento_tipo' => 'nullable|string|max:20',
            'documento_numero' => 'nullable|string|max:50',
            'firma' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        unset($data['firma']);

        if ($request->hasFile('firma')) {
            if ($estadia->firma) {
                Storage::disk('public')->delete($estadia->firma);
            }
            $data['firma'] = $request->file('firma')->store('firmas', 'public');
            $data['firmado_at'] = now();
        }

        $estadia->update($data);

        return response()->json($this->conRelaciones($habitacion->fresh()));
    }

    public function estimadoHospedaje(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'fecha_checkin' => 'nullable|date',
            'noches' => 'required|integer|min:1',
        ]);

        $resultado = $this->tarifas->calcularCargoHospedaje(
            $idTenant,
            (float) ($habitacion->precio ?? 0),
            $data['fecha_checkin'] ?? now()->toDateString(),
            $data['noches']
        );

        return response()->json($resultado);
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

    public function limpieza(Request $request, string $id)
    {
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'estado' => 'required|in:limpia,sucia,en_limpieza,inspeccion',
        ]);

        $habitacion->update(['estado_limpieza' => $data['estado']]);

        return response()->json($this->conRelaciones($habitacion));
    }

    public function incidencias(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $query = HabitacionIncidencia::where('id_tenant', $idTenant)
            ->with('habitacion:id,numero,tipo');

        if ($request->query('estado')) {
            $query->where('estado', $request->query('estado'));
        }

        return response()->json(
            $query->orderByRaw("estado = 'abierta' desc")->latest()->get()
        );
    }

    public function reportarIncidencia(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = $this->habitacionDelTenant($request, $id);

        $data = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'prioridad' => 'sometimes|in:baja,media,alta',
            'fuera_de_servicio' => 'sometimes|boolean',
        ]);

        $fueraDeServicio = $data['fuera_de_servicio'] ?? false;

        if ($fueraDeServicio) {
            if (! in_array($habitacion->estado, ['libre', 'mantenimiento'])) {
                return response()->json(['message' => 'No se puede poner fuera de servicio una habitación con estancia activa'], 422);
            }
            $habitacion->update(['estado' => 'mantenimiento']);
        }

        $incidencia = HabitacionIncidencia::create([
            'id_tenant' => $idTenant,
            'id_habitacion' => $habitacion->id,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'prioridad' => $data['prioridad'] ?? 'media',
            'fuera_de_servicio' => $fueraDeServicio,
            'estado' => 'abierta',
        ]);

        return response()->json($incidencia->load('habitacion:id,numero,tipo'), 201);
    }

    public function resolverIncidencia(Request $request, string $incidenciaId)
    {
        $idTenant = $request->user()->id_tenant;

        $incidencia = HabitacionIncidencia::where('id_tenant', $idTenant)->findOrFail($incidenciaId);

        if ($incidencia->estado === 'resuelta') {
            return response()->json(['message' => 'Esta incidencia ya está resuelta'], 422);
        }

        $incidencia->update(['estado' => 'resuelta', 'resuelta_at' => now()]);

        if ($incidencia->fuera_de_servicio) {
            $habitacion = Habitacion::where('id_tenant', $idTenant)->find($incidencia->id_habitacion);

            $quedanAbiertas = HabitacionIncidencia::where('id_tenant', $idTenant)
                ->where('id_habitacion', $incidencia->id_habitacion)
                ->where('fuera_de_servicio', true)
                ->where('estado', 'abierta')
                ->exists();

            if ($habitacion && $habitacion->estado === 'mantenimiento' && ! $quedanAbiertas) {
                $habitacion->update(['estado' => 'libre']);
            }
        }

        return response()->json($incidencia->load('habitacion:id,numero,tipo'));
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
        $cargoHospedaje = $habitacion->check_in
            ? $this->tarifas->calcularCargoHospedaje(
                $idTenant,
                (float) ($habitacion->precio ?? 0),
                $habitacion->check_in->toDateString(),
                (int) ($habitacion->noches ?? 0)
            )['total']
            : 0.0;

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
            'estado_limpieza' => 'sucia',
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

    public function reporteOcupacion(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = isset($data['desde']) ? Carbon::parse($data['desde'])->startOfDay() : now()->startOfMonth();
        $hasta = isset($data['hasta']) ? Carbon::parse($data['hasta'])->startOfDay() : now()->startOfDay();

        $totalHabitaciones = Habitacion::where('id_tenant', $idTenant)->count();

        $estadias = Estadia::where('id_tenant', $idTenant)
            ->with('habitacion:id,precio')
            ->whereDate('check_in', '<=', $hasta->toDateString())
            ->where(function ($q) use ($desde) {
                $q->whereNull('check_out_real')->orWhereDate('check_out_real', '>=', $desde->toDateString());
            })
            ->get();

        $nochesVendidas = 0;
        $ingresosHospedaje = 0.0;
        $porDia = [];

        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $ocupadasHoy = 0;

            foreach ($estadias as $estadia) {
                $checkIn = Carbon::parse($estadia->check_in);
                $limite = $estadia->check_out_real ? Carbon::parse($estadia->check_out_real) : Carbon::parse($estadia->check_out_programado);

                if ($checkIn->lte($d) && $limite->gt($d)) {
                    $ocupadasHoy++;
                    $noches = max((int) $estadia->noches, 1);
                    $tarifaNoche = $estadia->total_hospedaje !== null
                        ? $estadia->total_hospedaje / $noches
                        : (float) ($estadia->habitacion?->precio ?? 0);
                    $ingresosHospedaje += $tarifaNoche;
                }
            }

            $nochesVendidas += $ocupadasHoy;
            $porDia[] = ['fecha' => $d->toDateString(), 'ocupadas' => $ocupadasHoy, 'total_habitaciones' => $totalHabitaciones];
        }

        $numDias = $desde->diffInDays($hasta) + 1;
        $nochesDisponibles = $totalHabitaciones * $numDias;

        // Ingresos por sección (Bar, Restaurante, Spa...): une lo cobrado directo en mesa
        // (MesaController::cobrar) y lo cargado a la habitación y liquidado en el check-out
        // (HabitacionController::checkOut) — ambos etiquetan PedidoItem.seccion con la misma
        // categoría, así que una sola agregación ya representa el negocio completo de esa sección
        // sin importar cómo pagó el cliente. 'Hospedaje' se excluye porque ya está en
        // ingresos_hospedaje arriba (y prorrateado por noche, no por fecha del pedido).
        $ingresosPorSeccion = PedidoItem::whereHas('pedido', function ($q) use ($idTenant, $desde, $hasta) {
                $q->where('id_tenant', $idTenant)
                    ->whereDate('fecha', '>=', $desde->toDateString())
                    ->whereDate('fecha', '<=', $hasta->toDateString());
            })
            ->whereNotNull('seccion')
            ->where('seccion', '!=', 'Hospedaje')
            ->selectRaw('seccion, SUM(subtotal) as total')
            ->groupBy('seccion')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($fila) => ['seccion' => $fila->seccion, 'total' => round((float) $fila->total, 2)]);

        return response()->json([
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'total_habitaciones' => $totalHabitaciones,
            'noches_disponibles' => $nochesDisponibles,
            'noches_vendidas' => $nochesVendidas,
            'ingresos_hospedaje' => round($ingresosHospedaje, 2),
            'ocupacion_pct' => $nochesDisponibles > 0 ? round(($nochesVendidas / $nochesDisponibles) * 100, 1) : 0,
            'adr' => $nochesVendidas > 0 ? round($ingresosHospedaje / $nochesVendidas, 2) : 0,
            'revpar' => $nochesDisponibles > 0 ? round($ingresosHospedaje / $nochesDisponibles, 2) : 0,
            'por_dia' => $porDia,
            'ingresos_por_seccion' => $ingresosPorSeccion,
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

    public function historialCliente(Request $request, string $idCliente)
    {
        $estadias = Estadia::where('id_tenant', $request->user()->id_tenant)
            ->where('id_cliente', $idCliente)
            ->with('habitacion:id,numero,tipo')
            ->orderByDesc('check_in')
            ->get();

        return response()->json([
            'total_estadias' => $estadias->count(),
            'estadias' => $estadias,
        ]);
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
