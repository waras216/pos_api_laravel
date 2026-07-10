<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Movimiento::where('id_tenant', $request->user()->id_tenant)
                ->latest('fecha')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'concepto' => 'required|string|max:200',
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0',
            'categoria' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['fecha'] = $data['fecha'] ?? now()->toDateString();

        return response()->json(Movimiento::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Movimiento::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function destroy(Request $request, string $id)
    {
        $mov = Movimiento::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $mov->delete();

        return response()->json(['message' => 'Movimiento eliminado']);
    }

    public function papelera(Request $request)
    {
        return response()->json(
            Movimiento::onlyTrashed()
                ->where('id_tenant', $request->user()->id_tenant)
                ->latest('deleted_at')
                ->get()
        );
    }

    public function restaurar(Request $request, string $id)
    {
        $mov = Movimiento::onlyTrashed()->where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $mov->restore();

        return response()->json($mov);
    }
}
