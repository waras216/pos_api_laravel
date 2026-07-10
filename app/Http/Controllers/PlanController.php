<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Plan::withCount('tenants')->orderBy('id_plan')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_plan' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'max_usuarios' => 'nullable|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        return response()->json(Plan::create($data), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);

        $data = $request->validate([
            'nombre_plan' => 'sometimes|string|max:100',
            'precio' => 'sometimes|numeric|min:0',
            'max_usuarios' => 'nullable|integer|min:1',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date',
        ]);

        $plan->update($data);
        return response()->json($plan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->tenants()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un plan con tenants asignados'], 422);
        }

        $plan->delete();
        return response()->json(['message' => 'Plan eliminado correctamente']);
    }
}
