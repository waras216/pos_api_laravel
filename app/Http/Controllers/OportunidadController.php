<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Notificacion;
use App\Models\Oportunidad;
use App\Services\Crm\AutomatizacionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OportunidadController extends Controller
{
    public function __construct(private AutomatizacionEngine $automatizaciones) {}

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
     * Update only the kanban stage of the opportunity. Moving into 'cierre'
     * requires an explicit estado (ganada/perdida) and triggers a real
     * side effect: an audit Actividad and a Notificacion for the deal owner.
     * Moving away from 'cierre' reopens the deal (estado back to abierta).
     */
    public function moverEtapa(Request $request, string $id)
    {
        $data = $request->validate([
            'etapa' => 'required|in:prospeccion,contacto,propuesta,negociacion,cierre',
            'estado' => 'nullable|in:abierta,ganada,perdida',
        ]);

        if ($data['etapa'] === 'cierre' && !in_array($data['estado'] ?? null, ['ganada', 'perdida'], true)) {
            abort(422, 'Para mover a Cierre debes indicar si la oportunidad fue ganada o perdida.');
        }

        return DB::transaction(function () use ($request, $id, $data) {
            $oportunidad = Oportunidad::where('id_oportunidad', $id)
                ->where('id_tenant', $request->user()->id_tenant)
                ->lockForUpdate()
                ->firstOrFail();

            if ($data['etapa'] === 'cierre') {
                $oportunidad->update([
                    'etapa' => 'cierre',
                    'estado' => $data['estado'],
                    'fecha_cierre' => now(),
                ]);

                $gano = $data['estado'] === 'ganada';

                Actividad::create([
                    'id_tenant' => $request->user()->id_tenant,
                    'id_usuario' => $oportunidad->id_usuario,
                    'id_cliente' => $oportunidad->id_cliente,
                    'id_oportunidad' => $oportunidad->id_oportunidad,
                    'tipo' => 'nota',
                    'titulo' => $gano ? 'Oportunidad ganada' : 'Oportunidad perdida',
                    'estado' => 'completada',
                ]);

                Notificacion::create([
                    'id_tenant' => $request->user()->id_tenant,
                    'id_usuario' => $oportunidad->id_usuario,
                    'id_cliente' => $oportunidad->id_cliente,
                    'titulo' => $gano ? '🎉 Oportunidad ganada' : 'Oportunidad perdida',
                    'mensaje' => sprintf(
                        '"%s" con %s por $%s fue marcada como %s.',
                        $oportunidad->titulo,
                        $oportunidad->cliente->nombre ?? 'cliente',
                        number_format((float) $oportunidad->valor, 2),
                        $gano ? 'ganada' : 'perdida'
                    ),
                    'tipo' => $gano ? 'success' : 'warning',
                    'url' => '/crm/oportunidades',
                ]);

                $this->automatizaciones->disparar(
                    $gano ? 'oportunidad_ganada' : 'oportunidad_perdida',
                    $request->user()->id_tenant,
                    ['oportunidad' => $oportunidad]
                );
            } else {
                $oportunidad->update([
                    'etapa' => $data['etapa'],
                    'estado' => 'abierta',
                    'fecha_cierre' => null,
                ]);

                $this->automatizaciones->disparar(
                    'oportunidad_etapa_cambiada',
                    $request->user()->id_tenant,
                    ['oportunidad' => $oportunidad]
                );
            }

            return response()->json($oportunidad->load(['cliente', 'pipeline', 'usuario']));
        });
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
