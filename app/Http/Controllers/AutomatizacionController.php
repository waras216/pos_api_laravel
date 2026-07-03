<?php

namespace App\Http\Controllers;

use App\Models\Automatizacion;
use Illuminate\Http\Request;

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
            'regla' => 'required|string|max:255',
            'evento' => 'required|string|max:100',
            'accion' => 'required|string|max:150',
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
            'regla' => 'sometimes|string|max:255',
            'evento' => 'sometimes|string|max:100',
            'accion' => 'sometimes|string|max:150',
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
