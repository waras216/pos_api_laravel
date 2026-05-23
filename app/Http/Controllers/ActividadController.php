<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Oportunidad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Actividad::where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'lead', 'oportunidad', 'usuario'])
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'id_lead' => 'nullable|exists:leads,id_lead',
            'id_oportunidad' => 'nullable|exists:oportunidades,id_oportunidad',
            'tipo' => 'required|in:llamada,reunion,email,tarea,nota',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'sometimes|in:pendiente,completada,canceleda',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',        
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;

        return response()->json(Actividad::create($data),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Actividad::where('id_actividad', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'lead', 'oportunidad', 'usuario'])
            ->firstOrFail()
        );        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $actividad = Actividad::where('id_actividad', $id)
        ->where('id_tenant',$request->user()->id_tenant)
        ->firstOrFail();

        $data = $request->validate([
            'id_cliente' => 'nullable|exists:clientes, id_cliente',
            'id_lead' => 'nullable|exists:leads,id_lead',
            'id_oportunidad' => 'nullable|exists:oportunidades,id_oportunidad',
            'tipo' => 'required|in:llamada,reunion,email,tarea,nota',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'sometimes|in:pendiente,completada,canceleda',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',        
        ]);

        $actividad->update($data);
        return response()->json($actividad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $actividad = Actividad::where('id_actividad', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $actividad->delete();
        return response()->json(['message' => 'Actividad eliminada']);
    }
}
