<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Caja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Caja::where('id_tenant', $request->user()->id_tenant)->with('sucursal')->orderBy('nombre')->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'id_sucursal' => ['required', Rule::exists('erp_sucursales', 'id_sucursal')->where('id_tenant', $idTenant)],
        ]);

        $data['id_tenant'] = $idTenant;

        return response()->json(Caja::create($data)->load('sucursal'), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Caja::where('id_tenant', $request->user()->id_tenant)->with('sucursal')->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $caja = Caja::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'id_sucursal' => ['sometimes', Rule::exists('erp_sucursales', 'id_sucursal')->where('id_tenant', $idTenant)],
            'activo' => 'sometimes|boolean',
        ]);

        $caja->update($data);

        return response()->json($caja->load('sucursal'));
    }

    public function destroy(Request $request, string $id)
    {
        $caja = Caja::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($caja->turnos()->where('estado', 'abierto')->exists()) {
            return response()->json(['message' => 'No se puede eliminar: tiene un turno abierto.'], 422);
        }

        $caja->delete();

        return response()->json(['message' => 'Caja eliminada']);
    }
}
