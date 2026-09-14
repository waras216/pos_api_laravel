<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Sucursal::where('id_tenant', $request->user()->id_tenant)->orderBy('nombre')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:250',
            'telefono' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:150',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        return response()->json(Sucursal::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Sucursal::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $sucursal = Sucursal::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'direccion' => 'nullable|string|max:250',
            'telefono' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:150',
            'activo' => 'sometimes|boolean',
        ]);

        $sucursal->update($data);

        return response()->json($sucursal);
    }

    public function destroy(Request $request, string $id)
    {
        $sucursal = Sucursal::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($sucursal->cajas()->exists()) {
            return response()->json(['message' => 'No se puede eliminar: tiene cajas asignadas.'], 422);
        }

        $sucursal->delete();

        return response()->json(['message' => 'Sucursal eliminada']);
    }
}
