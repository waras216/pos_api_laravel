<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Caja;
use App\Models\Erp\TurnoCaja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TurnoCajaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            TurnoCaja::where('id_tenant', $request->user()->id_tenant)
                ->with(['caja.sucursal', 'cajero'])
                ->latest('fecha_apertura')
                ->get()
        );
    }

    public function abrir(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_caja' => ['required', Rule::exists('erp_cajas', 'id_caja')->where('id_tenant', $idTenant)],
            'monto_apertura' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:350',
        ]);

        if (TurnoCaja::where('id_caja', $data['id_caja'])->where('estado', 'abierto')->exists()) {
            return response()->json(['message' => 'Esta caja ya tiene un turno abierto.'], 422);
        }

        $turno = TurnoCaja::create([
            'id_tenant' => $idTenant,
            'id_caja' => $data['id_caja'],
            'id_usuario' => $request->user()->id_usuario,
            'fecha_apertura' => now(),
            'monto_apertura' => $data['monto_apertura'],
            'estado' => 'abierto',
            'notas' => $data['notas'] ?? null,
        ]);

        return response()->json($turno->load(['caja.sucursal', 'cajero']), 201);
    }

    public function cerrar(Request $request, string $id)
    {
        $turno = TurnoCaja::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($turno->estado !== 'abierto') {
            return response()->json(['message' => 'Este turno ya está cerrado.'], 422);
        }

        $data = $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:350',
        ]);

        $turno->update([
            'monto_cierre' => $data['monto_cierre'],
            'diferencia' => $data['monto_cierre'] - $turno->monto_apertura,
            'fecha_cierre' => now(),
            'estado' => 'cerrado',
            'notas' => $data['notas'] ?? $turno->notas,
        ]);

        return response()->json($turno->load(['caja.sucursal', 'cajero']));
    }
}
