<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Asiento;
use App\Models\Erp\AsientoDetalle;
use App\Models\Erp\Comanda;
use App\Models\Erp\Empleado;
use App\Models\Erp\Envio;
use App\Models\Erp\Habitacion;
use App\Models\Erp\Mesa;
use App\Models\Erp\OrdenCompra;
use App\Models\Erp\OrdenProduccion;
use App\Models\Erp\Pedido;
use App\Models\Erp\Proyecto;
use App\Models\Erp\Receta;
use App\Models\Producto;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Mismo criterio que ERP_TABS_OCULTOS_POR_NICHO en module.service.ts
    // (frontend) — mantener sincronizados.
    private const NICHOS_SIN_FABRICACION = ['restaurante', 'hotel', 'farmacia', 'tienda', 'startup'];
    private const NICHOS_SIN_SCM = ['restaurante', 'hotel', 'farmacia', 'startup'];
    private const NICHOS_SIN_COMPRAS_INVENTARIO = ['startup'];

    public function resumen(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $nicho = Tenant::with('negocio')->find($idTenant)?->negocio?->slug;

        $inventario = Producto::where('id_tenant', $idTenant)->get();
        $compras = OrdenCompra::where('id_tenant', $idTenant)->with('proveedor')->get();
        $ventas = Pedido::where('id_tenant', $idTenant)->with('cliente')->get();
        $empleados = Empleado::where('id_tenant', $idTenant)->get();
        $produccion = OrdenProduccion::where('id_tenant', $idTenant)->get();
        $envios = Envio::where('id_tenant', $idTenant)->get();
        $proyectos = Proyecto::where('id_tenant', $idTenant)->get();

        // Ingresos/egresos del mes se leen del libro mayor (partida doble)
        // en vez de la vieja tabla plana erp_movimientos.
        $lineasMes = AsientoDetalle::whereHas('asiento', fn ($q) => $q->where('id_tenant', $idTenant)
            ->whereYear('fecha', now()->year)
            ->whereMonth('fecha', now()->month))
            ->whereHas('cuenta', fn ($q) => $q->whereIn('tipo', ['ingreso', 'costo', 'gasto']))
            ->with('cuenta')
            ->get();

        $valorInventario = $inventario->sum(fn ($p) => $p->precio * $p->stock);
        $comprasPendientes = $compras->where('estado', 'pendiente');
        $ingresosMes = $lineasMes->where('cuenta.tipo', 'ingreso')->sum(fn ($l) => $l->haber - $l->debe);
        $egresosMes = $lineasMes->whereIn('cuenta.tipo', ['costo', 'gasto'])->sum(fn ($l) => $l->debe - $l->haber);
        $ventasPorCobrar = $ventas->where('estado', '!=', 'facturado')->sum('total');
        $empleadosActivos = $empleados->where('estado', 'activo');
        $nominaMensual = $empleadosActivos->sum('salario');
        $produccionEnProceso = $produccion->where('estado', 'en proceso');
        $produccionCompletada = $produccion->where('estado', 'completada');
        $enviosTransito = $envios->where('estado', 'en_transito');
        $enviosEntregados = $envios->where('estado', 'entregado');
        $proyectosActivos = $proyectos->where('estado', 'activo');
        $horasProyectos = $proyectos->sum('horas');

        $kpis = [
            ['value' => '$' . number_format($valorInventario, 0), 'label' => 'Valor de Inventario', 'bg' => 'bg-amber-100', 'color' => 'text-amber-600', 'icon' => 'box'],
            ['value' => '$' . number_format($ingresosMes, 0), 'label' => 'Ingresos del Mes', 'bg' => 'bg-emerald-100', 'color' => 'text-emerald-600', 'icon' => 'dollar'],
            ['value' => (string) $comprasPendientes->count(), 'label' => 'Compras Pendientes', 'bg' => 'bg-blue-100', 'color' => 'text-blue-600', 'icon' => 'clipboard'],
            ['value' => (string) $proyectosActivos->count(), 'label' => 'Proyectos Activos', 'bg' => 'bg-purple-100', 'color' => 'text-purple-600', 'icon' => 'folder'],
        ];

        $modulos = [
            ['titulo' => 'Inventario', 'subtitulo' => 'Productos y stock', 'emoji' => '📦', 'bg' => 'bg-amber-100', 'stat' => $inventario->filter(fn ($p) => $p->stock <= $p->stock_minimo)->count() . ' con stock bajo', 'statColor' => 'text-red-500', 'extra' => $inventario->count() . ' productos'],
            ['titulo' => 'Compras', 'subtitulo' => 'Órdenes a proveedores', 'emoji' => '🛒', 'bg' => 'bg-orange-100', 'stat' => $comprasPendientes->count() . ' pendientes', 'statColor' => 'text-amber-600', 'extra' => '$' . number_format($comprasPendientes->sum('total'), 0) . ' por recibir'],
            ['titulo' => 'Finanzas', 'subtitulo' => 'Ingresos y egresos', 'emoji' => '💰', 'bg' => 'bg-emerald-100', 'stat' => '$' . number_format($ingresosMes - $egresosMes, 0) . ' balance', 'statColor' => ($ingresosMes - $egresosMes) >= 0 ? 'text-emerald-600' : 'text-red-600', 'extra' => 'Egresos $' . number_format($egresosMes, 0)],
            ['titulo' => 'Ventas', 'subtitulo' => 'Pedidos y facturación', 'emoji' => '🧾', 'bg' => 'bg-blue-100', 'stat' => $ventas->count() . ' pedidos', 'statColor' => 'text-blue-600', 'extra' => '$' . number_format($ventasPorCobrar, 0) . ' por cobrar'],
            ['titulo' => 'RRHH', 'subtitulo' => 'Personal y nómina', 'emoji' => '👥', 'bg' => 'bg-purple-100', 'stat' => $empleadosActivos->count() . ' activos', 'statColor' => 'text-purple-600', 'extra' => 'Nómina $' . number_format($nominaMensual, 0)],
            ['titulo' => 'Fabricación', 'subtitulo' => 'Órdenes de producción', 'emoji' => '🏭', 'bg' => 'bg-slate-100', 'stat' => $produccionEnProceso->count() . ' en proceso', 'statColor' => 'text-amber-600', 'extra' => $produccionCompletada->count() . ' completadas'],
            ['titulo' => 'SCM', 'subtitulo' => 'Envíos y logística', 'emoji' => '🚚', 'bg' => 'bg-teal-100', 'stat' => $enviosTransito->count() . ' en tránsito', 'statColor' => 'text-teal-600', 'extra' => $enviosEntregados->count() . ' entregados'],
            ['titulo' => 'Proyectos', 'subtitulo' => 'Planificación y horas', 'emoji' => '📁', 'bg' => 'bg-indigo-100', 'stat' => $proyectosActivos->count() . ' activos', 'statColor' => 'text-indigo-600', 'extra' => $horasProyectos . 'h registradas'],
        ];

        if (in_array($nicho, self::NICHOS_SIN_FABRICACION, true)) {
            $modulos = array_values(array_filter($modulos, fn ($m) => $m['titulo'] !== 'Fabricación'));
        }
        if (in_array($nicho, self::NICHOS_SIN_SCM, true)) {
            $modulos = array_values(array_filter($modulos, fn ($m) => $m['titulo'] !== 'SCM'));
        }
        if (in_array($nicho, self::NICHOS_SIN_COMPRAS_INVENTARIO, true)) {
            $modulos = array_values(array_filter($modulos, fn ($m) => ! in_array($m['titulo'], ['Compras', 'Inventario'], true)));
        }

        $actividadExtra = collect();

        if ($nicho === 'restaurante') {
            $mesas = Mesa::where('id_tenant', $idTenant)->get();
            $ocupadas = $mesas->whereIn('estado', ['ocupada', 'cuenta'])->count();

            $kpis[] = ['value' => "{$ocupadas}/{$mesas->count()}", 'label' => 'Mesas ocupadas', 'bg' => 'bg-red-100', 'color' => 'text-red-600', 'icon' => 'box'];
            $modulos[] = ['titulo' => 'Mesas', 'subtitulo' => 'Comandas y ocupación', 'emoji' => '🍽️', 'bg' => 'bg-red-100', 'stat' => $ocupadas . ' ocupadas', 'statColor' => 'text-red-600', 'extra' => $mesas->count() . ' mesas totales'];

            $actividadExtra = Comanda::where('id_tenant', $idTenant)->where('estado', 'cerrada')->with('mesa')
                ->latest('updated_at')->take(5)->get()
                ->map(fn ($c) => ['texto' => "Mesa {$c->mesa?->numero} cobrada — \$" . number_format($c->total, 0), 'dot' => 'bg-red-400', 'ts' => $c->updated_at]);
        } elseif ($nicho === 'hotel') {
            $habitaciones = Habitacion::where('id_tenant', $idTenant)->get();
            $ocupadas = $habitaciones->where('estado', 'ocupada')->count();

            $kpis[] = ['value' => "{$ocupadas}/{$habitaciones->count()}", 'label' => 'Habitaciones ocupadas', 'bg' => 'bg-blue-100', 'color' => 'text-blue-600', 'icon' => 'box'];
            $modulos[] = ['titulo' => 'Habitaciones', 'subtitulo' => 'Ocupación y check-ins', 'emoji' => '🛏️', 'bg' => 'bg-blue-100', 'stat' => $ocupadas . ' ocupadas', 'statColor' => 'text-blue-600', 'extra' => $habitaciones->count() . ' habitaciones totales'];

            $actividadExtra = $habitaciones->where('estado', 'ocupada')
                ->sortByDesc('updated_at')->take(5)
                ->map(fn ($h) => ['texto' => "Check-in: {$h->huesped} — Hab. {$h->numero}", 'dot' => 'bg-amber-400', 'ts' => $h->updated_at])
                ->values();
        } elseif ($nicho === 'farmacia') {
            $pendientes = Receta::where('id_tenant', $idTenant)->where('pendiente', true)->count();
            $total = Receta::where('id_tenant', $idTenant)->count();

            $kpis[] = ['value' => (string) $pendientes, 'label' => 'Recetas pendientes', 'bg' => 'bg-emerald-100', 'color' => 'text-emerald-600', 'icon' => 'clipboard'];
            $modulos[] = ['titulo' => 'Recetas', 'subtitulo' => 'Control y dispensación', 'emoji' => '💊', 'bg' => 'bg-emerald-100', 'stat' => $pendientes . ' pendientes', 'statColor' => 'text-amber-600', 'extra' => $total . ' recetas registradas'];

            $actividadExtra = Receta::where('id_tenant', $idTenant)->where('pendiente', false)->with('producto')
                ->latest('updated_at')->take(5)->get()
                ->map(fn ($r) => ['texto' => 'Receta dispensada: ' . ($r->producto?->nombre ?? 'producto'), 'dot' => 'bg-emerald-400', 'ts' => $r->updated_at]);
        }

        $asientosRecientes = Asiento::where('id_tenant', $idTenant)->latest('created_at')->take(10)->get();

        $actividad = collect()
            ->concat($inventario->map(fn ($i) => ['texto' => "Producto agregado a inventario: {$i->nombre}", 'dot' => 'bg-amber-400', 'ts' => $i->created_at]))
            ->concat($compras->map(fn ($o) => ['texto' => "Orden de compra a {$o->proveedor?->nombre} por \$" . number_format($o->total, 0), 'dot' => 'bg-blue-400', 'ts' => $o->created_at]))
            ->concat($asientosRecientes->map(fn ($a) => ['texto' => "Asiento contable: {$a->concepto}", 'dot' => $a->origen === 'ajuste' ? 'bg-red-400' : 'bg-emerald-400', 'ts' => $a->created_at]))
            ->concat($ventas->map(fn ($v) => ['texto' => "Pedido de venta para {$v->cliente?->nombre} por \$" . number_format($v->total, 0), 'dot' => 'bg-blue-400', 'ts' => $v->created_at]))
            ->concat($empleados->map(fn ($e) => ['texto' => "Nuevo empleado: {$e->nombre} ({$e->puesto})", 'dot' => 'bg-purple-400', 'ts' => $e->created_at]))
            ->concat($produccion->map(fn ($p) => ['texto' => "Orden de producción: {$p->producto} x{$p->cantidad}", 'dot' => 'bg-amber-400', 'ts' => $p->created_at]))
            ->concat($envios->map(fn ($e) => ['texto' => "Envío a {$e->destino} vía {$e->transportista}", 'dot' => 'bg-teal-400', 'ts' => $e->created_at]))
            ->concat($proyectos->map(fn ($p) => ['texto' => "Proyecto creado: {$p->nombre} ({$p->cliente})", 'dot' => 'bg-indigo-400', 'ts' => $p->created_at]))
            ->concat($actividadExtra)
            ->sortByDesc('ts')
            ->take(8)
            ->map(fn ($a) => ['texto' => $a['texto'], 'dot' => $a['dot'], 'tiempo' => $a['ts']->diffForHumans()])
            ->values();

        return response()->json([
            'kpis' => $kpis,
            'modulos' => $modulos,
            'actividad' => $actividad,
        ]);
    }
}
