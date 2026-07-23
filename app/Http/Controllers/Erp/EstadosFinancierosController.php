<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\CuentaContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadosFinancierosController extends Controller
{
    /**
     * Balance de comprobación: por cada cuenta movible, Σdebe, Σhaber y
     * saldo (según su naturaleza) dentro del rango de fechas dado.
     */
    public function balanceComprobacion(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $filas = $this->sumasPorCuenta($idTenant, $request->query('desde'), $request->query('hasta'));

        $filas = $filas->map(function ($fila) {
            $saldo = $fila->naturaleza === 'deudora'
                ? $fila->debe - $fila->haber
                : $fila->haber - $fila->debe;

            return [
                'id_cuenta' => $fila->id,
                'codigo' => $fila->codigo,
                'nombre' => $fila->nombre,
                'tipo' => $fila->tipo,
                'naturaleza' => $fila->naturaleza,
                'debe' => round($fila->debe, 2),
                'haber' => round($fila->haber, 2),
                'saldo' => round($saldo, 2),
            ];
        })->values();

        return response()->json([
            'cuentas' => $filas,
            'total_debe' => round($filas->sum('debe'), 2),
            'total_haber' => round($filas->sum('haber'), 2),
        ]);
    }

    /**
     * Estado de resultados: ingresos − costos − gastos del período.
     */
    public function estadoResultados(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $filas = $this->sumasPorCuenta($idTenant, $request->query('desde'), $request->query('hasta'));

        $seccion = fn (string $tipo) => $filas->where('tipo', $tipo)->map(fn ($f) => [
            'codigo' => $f->codigo,
            'nombre' => $f->nombre,
            'monto' => round($f->naturaleza === 'acreedora' ? $f->haber - $f->debe : $f->debe - $f->haber, 2),
        ])->values();

        $ingresos = $seccion('ingreso');
        $costos = $seccion('costo');
        $gastos = $seccion('gasto');

        $totalIngresos = round($ingresos->sum('monto'), 2);
        $totalCostos = round($costos->sum('monto'), 2);
        $totalGastos = round($gastos->sum('monto'), 2);

        return response()->json([
            'ingresos' => $ingresos,
            'costos' => $costos,
            'gastos' => $gastos,
            'total_ingresos' => $totalIngresos,
            'total_costos' => $totalCostos,
            'total_gastos' => $totalGastos,
            'utilidad_neta' => round($totalIngresos - $totalCostos - $totalGastos, 2),
        ]);
    }

    /**
     * Balance general a una fecha de corte: activo = pasivo + capital +
     * resultado del ejercicio (no hay asiento de cierre de período en v1,
     * así que el resultado acumulado se calcula y se muestra como una línea
     * más dentro de capital para que la ecuación siga balanceando).
     */
    public function balanceGeneral(Request $request)
    {
        $idTenant = $request->user()->id_tenant;
        $corte = $request->query('corte') ?? now()->toDateString();

        $filas = $this->sumasPorCuenta($idTenant, null, $corte);

        $seccion = fn (string $tipo) => $filas->where('tipo', $tipo)->map(fn ($f) => [
            'codigo' => $f->codigo,
            'nombre' => $f->nombre,
            'saldo' => round($f->naturaleza === 'deudora' ? $f->debe - $f->haber : $f->haber - $f->debe, 2),
        ])->values();

        $activo = $seccion('activo');
        $pasivo = $seccion('pasivo');
        $capital = $seccion('capital');

        $ingresos = $filas->where('tipo', 'ingreso')->sum(fn ($f) => $f->haber - $f->debe);
        $costosGastos = $filas->whereIn('tipo', ['costo', 'gasto'])->sum(fn ($f) => $f->debe - $f->haber);
        $resultadoEjercicio = round($ingresos - $costosGastos, 2);

        $totalActivo = round($activo->sum('saldo'), 2);
        $totalPasivo = round($pasivo->sum('saldo'), 2);
        $totalCapital = round($capital->sum('saldo') + $resultadoEjercicio, 2);

        return response()->json([
            'corte' => $corte,
            'activo' => $activo,
            'pasivo' => $pasivo,
            'capital' => $capital,
            'resultado_ejercicio' => $resultadoEjercicio,
            'total_activo' => $totalActivo,
            'total_pasivo' => $totalPasivo,
            'total_capital' => $totalCapital,
            'cuadra' => round($totalActivo - ($totalPasivo + $totalCapital), 2) === 0.0,
        ]);
    }

    private function sumasPorCuenta(int $idTenant, ?string $desde, ?string $hasta)
    {
        $query = CuentaContable::where('erp_plan_cuentas.id_tenant', $idTenant)
            ->where('es_movible', true)
            ->leftJoin('erp_asiento_detalles', 'erp_asiento_detalles.id_cuenta', '=', 'erp_plan_cuentas.id')
            ->leftJoin('erp_asientos', function ($join) use ($desde, $hasta) {
                $join->on('erp_asientos.id', '=', 'erp_asiento_detalles.id_asiento');
                if ($desde) {
                    $join->where('erp_asientos.fecha', '>=', $desde);
                }
                if ($hasta) {
                    $join->where('erp_asientos.fecha', '<=', $hasta);
                }
            })
            ->groupBy('erp_plan_cuentas.id', 'erp_plan_cuentas.codigo', 'erp_plan_cuentas.nombre', 'erp_plan_cuentas.tipo', 'erp_plan_cuentas.naturaleza')
            ->orderBy('erp_plan_cuentas.codigo')
            ->selectRaw('erp_plan_cuentas.id, erp_plan_cuentas.codigo, erp_plan_cuentas.nombre, erp_plan_cuentas.tipo, erp_plan_cuentas.naturaleza, COALESCE(SUM(erp_asiento_detalles.debe), 0) as debe, COALESCE(SUM(erp_asiento_detalles.haber), 0) as haber');

        return $query->get();
    }
}
