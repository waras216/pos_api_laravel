<?php

namespace App\Http\Controllers;

use App\Models\Campana;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Campana::where('id_tenant', $request->user()->id_tenant)
                ->with('clientes')
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_compania' => 'required|string|max:150',
            'segmento' => 'required|string|max:150',
            'id_clientes' => 'sometimes|array',
            'id_clientes.*' => 'integer|exists:clientes,id_cliente',
            'estado' => 'sometimes|in:activa,pausada,finalizada',
            'fecha_inicio' => 'nullable|date',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $idClientes = $data['id_clientes'] ?? [];
        unset($data['id_clientes']);

        $campana = Campana::create($data);
        $campana->clientes()->sync($idClientes);

        return response()->json($campana->load('clientes'), 201);
    }

    public function update(Request $request, string $id)
    {
        $campana = Campana::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $data = $request->validate([
            'nombre_compania' => 'sometimes|string|max:150',
            'segmento' => 'sometimes|string|max:150',
            'id_clientes' => 'sometimes|array',
            'id_clientes.*' => 'integer|exists:clientes,id_cliente',
            'estado' => 'sometimes|in:activa,pausada,finalizada',
            'fecha_inicio' => 'nullable|date',
        ]);

        if (array_key_exists('id_clientes', $data)) {
            $campana->clientes()->sync($data['id_clientes']);
            unset($data['id_clientes']);
        }

        $campana->update($data);

        return response()->json($campana->load('clientes'));
    }

    public function destroy(Request $request, string $id)
    {
        $campana = Campana::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $campana->delete();

        return response()->json(['message' => 'Campaña eliminada']);
    }
}
