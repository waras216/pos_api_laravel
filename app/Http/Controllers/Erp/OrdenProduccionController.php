<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\OrdenProduccion;
use Illuminate\Http\Request;

class OrdenProduccionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            OrdenProduccion::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'producto' => 'required|string|max:150',
            'cantidad' => 'required|integer|min:0',
            'progreso' => 'nullable|integer|min:0|max:100',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['progreso'] = $data['progreso'] ?? 0;
        $data['estado'] = ($data['progreso'] >= 100) ? 'completada' : 'en proceso';

        return response()->json(OrdenProduccion::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            OrdenProduccion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $orden = OrdenProduccion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'producto' => 'sometimes|string|max:150',
            'cantidad' => 'sometimes|integer|min:0',
            'progreso' => 'sometimes|integer|min:0|max:100',
            'estado' => 'sometimes|in:en proceso,completada',
        ]);

        if (isset($data['progreso']) && !isset($data['estado'])) {
            $data['estado'] = $data['progreso'] >= 100 ? 'completada' : 'en proceso';
        }

        $orden->update($data);

        return response()->json($orden);
    }

    public function destroy(Request $request, string $id)
    {
        $orden = OrdenProduccion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $orden->delete();

        return response()->json(['message' => 'Orden eliminada']);
    }
}
