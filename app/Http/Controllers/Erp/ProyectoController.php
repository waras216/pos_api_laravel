<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Proyecto::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'cliente' => 'required|string|max:150',
            'responsable' => 'required|string|max:150',
            'presupuesto' => 'nullable|numeric|min:0',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['estado'] = 'activo';
        $data['progreso'] = 0;
        $data['horas'] = 0;

        return response()->json(Proyecto::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Proyecto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $proyecto = Proyecto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'cliente' => 'sometimes|string|max:150',
            'responsable' => 'sometimes|string|max:150',
            'estado' => 'sometimes|in:activo,pausado,completado',
            'progreso' => 'sometimes|integer|min:0|max:100',
            'horas' => 'sometimes|integer|min:0',
            'presupuesto' => 'nullable|numeric|min:0',
        ]);

        $proyecto->update($data);

        return response()->json($proyecto);
    }

    public function destroy(Request $request, string $id)
    {
        $proyecto = Proyecto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $proyecto->delete();

        return response()->json(['message' => 'Proyecto eliminado']);
    }
}
