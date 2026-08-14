<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Empleado::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'departamento' => 'required|string|max:100',
            'puesto' => 'required|string|max:100',
            'salario' => 'nullable|numeric|min:0',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['estado'] = 'activo';

        return response()->json(Empleado::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Empleado::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $empleado = Empleado::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'departamento' => 'sometimes|string|max:100',
            'puesto' => 'sometimes|string|max:100',
            'estado' => 'sometimes|in:activo,inactivo',
            'salario' => 'nullable|numeric|min:0',
        ]);

        $empleado->update($data);

        return response()->json($empleado);
    }

    public function destroy(Request $request, string $id)
    {
        $empleado = Empleado::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $empleado->delete();

        return response()->json(['message' => 'Empleado eliminado']);
    }

    public function papelera(Request $request)
    {
        return response()->json(
            Empleado::onlyTrashed()
                ->where('id_tenant', $request->user()->id_tenant)
                ->latest('deleted_at')
                ->get()
        );
    }

    public function restaurar(Request $request, string $id)
    {
        $empleado = Empleado::onlyTrashed()->where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $empleado->restore();

        return response()->json($empleado);
    }
}
