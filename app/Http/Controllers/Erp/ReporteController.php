<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Movimiento;
use App\Models\Erp\OrdenCompra;
use App\Models\Erp\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReporteController extends Controller
{
    public function resumen(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);
        $desde = $data['desde'] ?? null;
        $hasta = $data['hasta'] ?? null;

        $inventario = Producto::where('id_tenant', $idTenant)->with('categoria')->get();

        $compras = OrdenCompra::where('id_tenant', $idTenant)->with('proveedor')
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->get();

        $ventas = Pedido::where('id_tenant', $idTenant)
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->get();

        $movimientos = Movimiento::where('id_tenant', $idTenant)
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->get();

        $ingresosTotal = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresosTotal = $movimientos->where('tipo', 'egreso')->sum('monto');

        return response()->json([
            'kpis' => [
                'valorInventario' => $inventario->sum(fn ($p) => $p->precio * $p->stock),
                'ingresosTotal' => $ingresosTotal,
                'egresosTotal' => $egresosTotal,
                'balance' => $ingresosTotal - $egresosTotal,
                'comprasPendientes' => $compras->where('estado', 'pendiente')->sum('total'),
                'ventasPorCobrar' => $ventas->where('estado', '!=', 'facturado')->sum('total'),
            ],
            'inventarioPorCategoria' => $inventario
                ->groupBy(fn ($p) => $p->categoria->nombre ?? 'Sin categoría')
                ->map->count(),
            'comprasPorEstado' => $compras->groupBy('estado')->map->count(),
            'comprasPorProveedor' => $compras
                ->groupBy(fn ($c) => $c->proveedor->nombre ?? 'Sin proveedor')
                ->map(fn ($g) => $g->sum('total')),
            'ventasPorEstado' => $ventas->groupBy('estado')->map->count(),
            'movimientosPorCategoria' => $movimientos->groupBy('categoria')->map(fn ($g) => $g->sum('monto')),
            'movimientosPorMes' => $movimientos
                ->groupBy(fn ($m) => Carbon::parse($m->fecha)->format('Y-m'))
                ->sortKeys()
                ->map(fn ($g) => [
                    'ingresos' => $g->where('tipo', 'ingreso')->sum('monto'),
                    'egresos' => $g->where('tipo', 'egreso')->sum('monto'),
                ])
                ->map(fn ($v, $mes) => ['mes' => $mes] + $v)
                ->values(),
        ]);
    }
}
