<?php

namespace App\Http\Controllers;

use App\Models\Automatizacion;
use App\Services\Crm\AutomatizacionEngine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AutomatizacionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Automatizacion::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_automatizacion' => 'required|string|max:150',
            'regla' => 'nullable|string|max:255',
            'evento' => ['required', Rule::in(AutomatizacionEngine::EVENTOS)],
            'accion' => ['required', Rule::in(AutomatizacionEngine::ACCIONES)],
            'parametros' => 'nullable|array',
            'activa' => 'sometimes|boolean',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        return response()->json(Automatizacion::create($data), 201);
    }

    public function update(Request $request, string $id)
    {
        $automatizacion = Automatizacion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $data = $request->validate([
            'nombre_automatizacion' => 'sometimes|string|max:150',
            'regla' => 'nullable|string|max:255',
            'evento' => ['sometimes', Rule::in(AutomatizacionEngine::EVENTOS)],
            'accion' => ['sometimes', Rule::in(AutomatizacionEngine::ACCIONES)],
            'parametros' => 'nullable|array',
            'activa' => 'sometimes|boolean',
        ]);

        $automatizacion->update($data);

        return response()->json($automatizacion);
    }

    public function toggle(Request $request, string $id)
    {
        $automatizacion = Automatizacion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $automatizacion->update(['activa' => ! $automatizacion->activa]);

        return response()->json($automatizacion);
    }

    public function destroy(Request $request, string $id)
    {
        $automatizacion = Automatizacion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $automatizacion->delete();

        return response()->json(['message' => 'Automatización eliminada']);
    }
}
