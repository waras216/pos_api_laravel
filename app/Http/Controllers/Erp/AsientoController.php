<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Asiento;
use App\Services\Erp\AsientoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AsientoController extends Controller
{
    public function __construct(private AsientoService $asientos) {}

    public function index(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $query = Asiento::where('id_tenant', $idTenant)->with('detalles.cuenta');

        if ($request->filled('desde')) {
            $query->where('fecha', '>=', $request->query('desde'));
        }
        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', $request->query('hasta'));
        }
        if ($request->filled('origen')) {
            $query->where('origen', $request->query('origen'));
        }

        return response()->json($query->latest('fecha')->latest('id')->get());
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Asiento::where('id_tenant', $request->user()->id_tenant)
                ->with('detalles.cuenta')
                ->findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:255',
            'lineas' => 'required|array|min:2',
            'lineas.*.id_cuenta' => [
                'required',
                Rule::exists('erp_plan_cuentas', 'id')->where('id_tenant', $idTenant)->where('es_movible', true),
            ],
            'lineas.*.debe' => 'nullable|numeric|min:0',
            'lineas.*.haber' => 'nullable|numeric|min:0',
            'lineas.*.descripcion' => 'nullable|string|max:255',
        ]);

        $asiento = $this->asientos->registrar(
            idTenant: $idTenant,
            fecha: $data['fecha'],
            concepto: $data['concepto'],
            origen: 'manual',
            lineas: $data['lineas'],
            idUsuario: $request->user()->id_usuario,
        );

        return response()->json($asiento, 201);
    }

    public function reversar(Request $request, string $id)
    {
        $asiento = Asiento::where('id_tenant', $request->user()->id_tenant)
            ->with('detalles')
            ->findOrFail($id);

        $reversa = $this->asientos->reversar($asiento, $request->user()->id_usuario);

        return response()->json($reversa, 201);
    }
}
