<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\AsientoDetalle;
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

        // Movimientos financieros ahora se leen del libro mayor (partida
        // doble) en vez de la vieja tabla plana erp_movimientos: solo las
        // líneas contra cuentas de ingreso/costo/gasto son relevantes para
        // este resumen tipo estado de resultados.
        $lineas = AsientoDetalle::whereHas('asiento', function ($q) use ($idTenant, $desde, $hasta) {
            $q->where('id_tenant', $idTenant)
                ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
                ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));
        })
            ->whereHas('cuenta', fn ($q) => $q->whereIn('tipo', ['ingreso', 'costo', 'gasto']))
            ->with(['cuenta', 'asiento'])
            ->get();

        $montoLinea = fn ($l) => $l->cuenta->tipo === 'ingreso' ? $l->haber - $l->debe : $l->debe - $l->haber;

        $ingresosTotal = $lineas->where('cuenta.tipo', 'ingreso')->sum($montoLinea);
        $egresosTotal = $lineas->whereIn('cuenta.tipo', ['costo', 'gasto'])->sum($montoLinea);

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
            'movimientosPorCategoria' => $lineas->groupBy(fn ($l) => $l->cuenta->nombre)->map(fn ($g) => $g->sum($montoLinea)),
            'movimientosPorMes' => $lineas
                ->groupBy(fn ($l) => Carbon::parse($l->asiento->fecha)->format('Y-m'))
                ->sortKeys()
                ->map(fn ($g) => [
                    'ingresos' => $g->where('cuenta.tipo', 'ingreso')->sum($montoLinea),
                    'egresos' => $g->whereIn('cuenta.tipo', ['costo', 'gasto'])->sum($montoLinea),
                ])
                ->map(fn ($v, $mes) => ['mes' => $mes] + $v)
                ->values(),
        ]);
    }
}
