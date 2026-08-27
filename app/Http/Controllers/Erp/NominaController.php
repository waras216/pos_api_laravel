<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Empleado;
use App\Models\Erp\NominaPago;
use App\Models\Erp\NominaPagoDetalle;
use App\Services\Erp\AsientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NominaController extends Controller
{
    public function __construct(private AsientoService $asientos) {}

    public function index(Request $request)
    {
        return response()->json(
            NominaPago::where('id_tenant', $request->user()->id_tenant)
                ->with('detalles.empleado')
                ->latest('fecha')
                ->get()
        );
    }

    /**
     * Procesa el pago de nómina del período indicado (semanal/quincenal/
     * mensual) para los empleados activos con esa periodicidad configurada
     * (o el subconjunto indicado en 'empleados'): registra el pago y genera
     * el asiento contable correspondiente (Gasto de Nómina vs Caja).
     */
    public function procesar(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'periodo' => 'nullable|in:semanal,quincenal,mensual',
            'fecha' => 'nullable|date',
            'fecha_inicio' => 'nullable|date',
            'empleados' => 'nullable|array|min:1',
            'empleados.*' => [
                Rule::exists('erp_empleados', 'id')->where('id_tenant', $idTenant),
            ],
        ]);

        $periodo = $data['periodo'] ?? 'mensual';
        $fecha = $data['fecha'] ?? now()->toDateString();
        $fechaInicio = $data['fecha_inicio'] ?? $this->inicioPeriodo($fecha, $periodo);

        // Solo se paga a empleados cuya periodicidad configurada coincide con
        // el período que se está procesando (una corrida semanal no debe
        // arrastrar empleados que cobran quincenal o mensual, y viceversa).
        $query = Empleado::where('id_tenant', $idTenant)
            ->where('estado', 'activo')
            ->where('periodicidad', $periodo);
        if (! empty($data['empleados'])) {
            $query->whereIn('id', $data['empleados']);
        }
        $empleados = $query->get();

        if ($empleados->isEmpty()) {
            return response()->json(['message' => 'No hay empleados activos con periodicidad "'.$periodo.'" para procesar'], 422);
        }

        $total = (float) $empleados->sum('salario');

        if ($total <= 0) {
            return response()->json(['message' => 'Los empleados seleccionados no tienen salario asignado'], 422);
        }

        $pago = DB::transaction(function () use ($idTenant, $fecha, $fechaInicio, $periodo, $total, $empleados) {
            $asiento = $this->asientos->registrarNomina($idTenant, $fecha, $total);

            $pago = NominaPago::create([
                'id_tenant' => $idTenant,
                'fecha' => $fecha,
                'periodo' => $periodo,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fecha,
                'total' => $total,
                'id_asiento' => $asiento->id,
            ]);

            foreach ($empleados as $empleado) {
                NominaPagoDetalle::create([
                    'id_nomina_pago' => $pago->id,
                    'id_empleado' => $empleado->id,
                    'salario' => $empleado->salario,
                ]);
            }

            return $pago;
        });

        return response()->json($pago->load('detalles.empleado'), 201);
    }

    /**
     * Calcula el inicio del período cubierto por una corrida de nómina a
     * partir de su fecha de corte, según la periodicidad seleccionada.
     */
    private function inicioPeriodo(string $fecha, string $periodo): string
    {
        return match ($periodo) {
            'semanal' => \Carbon\Carbon::parse($fecha)->subDays(6)->toDateString(),
            'quincenal' => \Carbon\Carbon::parse($fecha)->subDays(14)->toDateString(),
            default => \Carbon\Carbon::parse($fecha)->subMonth()->addDay()->toDateString(),
        };
    }
}
