<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Promocion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromocionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Promocion::where('id_tenant', $request->user()->id_tenant)
                ->with(['producto', 'categoria'])
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'tipo' => ['required', Rule::in(['porcentaje', 'monto_fijo'])],
            'valor' => 'required|numeric|min:0',
            'id_producto' => ['nullable', Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant)],
            'id_categorias' => ['nullable', Rule::exists('categorias', 'id_categoria')->where('id_tenant', $idTenant)],
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($data['tipo'] === 'porcentaje' && $data['valor'] > 100) {
            return response()->json(['message' => 'Un descuento porcentual no puede ser mayor a 100.'], 422);
        }

        $data['id_tenant'] = $idTenant;

        return response()->json(Promocion::create($data)->load(['producto', 'categoria']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Promocion::where('id_tenant', $request->user()->id_tenant)->with(['producto', 'categoria'])->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $promocion = Promocion::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'tipo' => ['sometimes', Rule::in(['porcentaje', 'monto_fijo'])],
            'valor' => 'sometimes|numeric|min:0',
            'id_producto' => ['nullable', Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant)],
            'id_categorias' => ['nullable', Rule::exists('categorias', 'id_categoria')->where('id_tenant', $idTenant)],
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'activo' => 'sometimes|boolean',
        ]);

        $promocion->update($data);

        return response()->json($promocion->load(['producto', 'categoria']));
    }

    public function toggle(Request $request, string $id)
    {
        $promocion = Promocion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $promocion->update(['activo' => ! $promocion->activo]);

        return response()->json($promocion->load(['producto', 'categoria']));
    }

    public function destroy(Request $request, string $id)
    {
        $promocion = Promocion::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $promocion->delete();

        return response()->json(['message' => 'Promoción eliminada']);
    }
}
