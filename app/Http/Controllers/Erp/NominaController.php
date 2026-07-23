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
     * Procesa el pago de nómina de los empleados indicados (o de todos los
     * activos si no se manda 'empleados'): registra el pago y genera el
     * asiento contable correspondiente (Gasto de Nómina vs Caja).
     */
    public function procesar(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'fecha' => 'nullable|date',
            'empleados' => 'nullable|array|min:1',
            'empleados.*' => [
                Rule::exists('erp_empleados', 'id')->where('id_tenant', $idTenant),
            ],
        ]);

        $query = Empleado::where('id_tenant', $idTenant)->where('estado', 'activo');
        if (! empty($data['empleados'])) {
            $query->whereIn('id', $data['empleados']);
        }
        $empleados = $query->get();

        if ($empleados->isEmpty()) {
            return response()->json(['message' => 'No hay empleados activos para procesar'], 422);
        }

        $fecha = $data['fecha'] ?? now()->toDateString();
        $total = (float) $empleados->sum('salario');

        if ($total <= 0) {
            return response()->json(['message' => 'Los empleados seleccionados no tienen salario asignado'], 422);
        }

        $pago = DB::transaction(function () use ($idTenant, $fecha, $total, $empleados) {
            $asiento = $this->asientos->registrarNomina($idTenant, $fecha, $total);

            $pago = NominaPago::create([
                'id_tenant' => $idTenant,
                'fecha' => $fecha,
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
}
