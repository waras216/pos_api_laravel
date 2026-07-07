<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Empleado;
use App\Models\Erp\Envio;
use App\Models\Erp\Inventario;
use App\Models\Erp\Movimiento;
use App\Models\Erp\OrdenCompra;
use App\Models\Erp\OrdenProduccion;
use App\Models\Erp\Pedido;
use App\Models\Erp\Proyecto;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function resumen(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $inventario = Inventario::where('id_tenant', $idTenant)->get();
        $compras = OrdenCompra::where('id_tenant', $idTenant)->get();
        $movimientos = Movimiento::where('id_tenant', $idTenant)->get();
        $ventas = Pedido::where('id_tenant', $idTenant)->get();
        $empleados = Empleado::where('id_tenant', $idTenant)->get();
        $produccion = OrdenProduccion::where('id_tenant', $idTenant)->get();
        $envios = Envio::where('id_tenant', $idTenant)->get();
        $proyectos = Proyecto::where('id_tenant', $idTenant)->get();

        $valorInventario = $inventario->sum(fn ($p) => $p->precio_venta * $p->stock);
        $comprasPendientes = $compras->where('estado', 'pendiente');
        $ingresosMes = $movimientos->where('tipo', 'ingreso')
            ->filter(fn ($m) => \Illuminate\Support\Carbon::parse($m->fecha)->isCurrentMonth())
            ->sum('monto');
        $egresosMes = $movimientos->where('tipo', 'egreso')
            ->filter(fn ($m) => \Illuminate\Support\Carbon::parse($m->fecha)->isCurrentMonth())
            ->sum('monto');
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

        $actividad = collect()
            ->concat($inventario->map(fn ($i) => ['texto' => "Producto agregado a inventario: {$i->nombre}", 'dot' => 'bg-amber-400', 'ts' => $i->created_at]))
            ->concat($compras->map(fn ($o) => ['texto' => "Orden de compra a {$o->proveedor} por \$" . number_format($o->total, 0), 'dot' => 'bg-blue-400', 'ts' => $o->created_at]))
            ->concat($movimientos->map(fn ($m) => ['texto' => ($m->tipo === 'ingreso' ? 'Ingreso registrado: ' : 'Egreso registrado: ') . $m->concepto, 'dot' => $m->tipo === 'ingreso' ? 'bg-emerald-400' : 'bg-red-400', 'ts' => $m->created_at]))
            ->concat($ventas->map(fn ($v) => ['texto' => "Pedido de venta para {$v->cliente} por \$" . number_format($v->total, 0), 'dot' => 'bg-blue-400', 'ts' => $v->created_at]))
            ->concat($empleados->map(fn ($e) => ['texto' => "Nuevo empleado: {$e->nombre} ({$e->puesto})", 'dot' => 'bg-purple-400', 'ts' => $e->created_at]))
            ->concat($produccion->map(fn ($p) => ['texto' => "Orden de producción: {$p->producto} x{$p->cantidad}", 'dot' => 'bg-amber-400', 'ts' => $p->created_at]))
            ->concat($envios->map(fn ($e) => ['texto' => "Envío a {$e->destino} vía {$e->transportista}", 'dot' => 'bg-teal-400', 'ts' => $e->created_at]))
            ->concat($proyectos->map(fn ($p) => ['texto' => "Proyecto creado: {$p->nombre} ({$p->cliente})", 'dot' => 'bg-indigo-400', 'ts' => $p->created_at]))
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
