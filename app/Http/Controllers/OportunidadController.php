<?php

namespace App\Http\Controllers;

use App\Models\Oportunidad;
use Illuminate\Http\Request;

class OportunidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Oportunidad::where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'pipeline', 'usuario'])
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_pipeline' => 'required|exists:pipelines,id_pipeline',
            'titulo' => 'required|string|max:200',
            'valor' => 'sometimes|numeric|min:0',
            'probabilidad' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:abierta,ganada,perdida',
            'etapa' => 'sometimes|in:prospeccion,contacto,propuesta,negociacion,cierre',
            'fecha_cierre' => 'nullable|date',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;

        return response()->json(Oportunidad::create($data)->load(['cliente', 'pipeline', 'usuario']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Oportunidad::where('id_oportunidad', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'pipeline', 'usuario', 'actividades'])
            ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $oportunidad = Oportunidad::where('id_oportunidad', $id)
        ->where('id_tenant',$request->user()->id_tenant)
        ->firstOrFail();

        $data = $request->validate([
            'id_cliente' => 'sometimes|exists:clientes,id_cliente',
            'id_pipeline' => 'sometimes|exists:pipelines,id_pipeline',
            'titulo' => 'sometimes|string|max:200',
            'valor' => 'sometimes|numeric|min:0',
            'probabilidad' => 'sometimes|integer|min:0|max:100',
            'estado' => 'sometimes|in:abierta,ganada,perdida',
            'etapa' => 'sometimes|in:prospeccion,contacto,propuesta,negociacion,cierre',
            'fecha_cierre' => 'nullable|date'
        ]);

        $oportunidad->update($data);

        return response()->json($oportunidad->load(['cliente', 'pipeline', 'usuario']));
    }

    /**
     * Update only the kanban stage of the opportunity.
     */
    public function moverEtapa(Request $request, string $id)
    {
        $oportunidad = Oportunidad::where('id_oportunidad', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $data = $request->validate([
            'etapa' => 'required|in:prospeccion,contacto,propuesta,negociacion,cierre',
        ]);

        $oportunidad->update($data);

        return response()->json($oportunidad->load(['cliente', 'pipeline', 'usuario']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $oportunidad = Oportunidad::where('id_oportunidad', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $oportunidad->delete();
        return response()->json(['message' => 'oportunidad eliminada']);
    }
}
