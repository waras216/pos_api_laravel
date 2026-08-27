<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Oportunidad;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function __construct(private GoogleCalendarService $calendario) {}

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
            'estado' => 'sometimes|in:pendiente,completada,cancelada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;

        $actividad = Actividad::create($data);

        if ($actividad->fecha_inicio) {
            $idEventoGoogle = $this->calendario->crearEvento(
                $actividad->id_tenant, $actividad->titulo, $actividad->descripcion, $actividad->fecha_inicio, $actividad->fecha_fin
            );
            if ($idEventoGoogle) {
                $actividad->update(['id_evento_google' => $idEventoGoogle]);
            }
        }

        return response()->json($actividad->load(['cliente', 'lead', 'oportunidad', 'usuario']),201);
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
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'id_lead' => 'nullable|exists:leads,id_lead',
            'id_oportunidad' => 'nullable|exists:oportunidades,id_oportunidad',
            'tipo' => 'sometimes|in:llamada,reunion,email,tarea,nota',
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'sometimes|in:pendiente,completada,cancelada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $actividad->update($data);

        if ($actividad->id_evento_google) {
            $this->calendario->actualizarEvento(
                $actividad->id_tenant, $actividad->id_evento_google, $actividad->titulo, $actividad->descripcion, $actividad->fecha_inicio, $actividad->fecha_fin
            );
        } elseif ($actividad->fecha_inicio) {
            $idEventoGoogle = $this->calendario->crearEvento(
                $actividad->id_tenant, $actividad->titulo, $actividad->descripcion, $actividad->fecha_inicio, $actividad->fecha_fin
            );
            if ($idEventoGoogle) {
                $actividad->update(['id_evento_google' => $idEventoGoogle]);
            }
        }

        return response()->json($actividad->load(['cliente', 'lead', 'oportunidad', 'usuario']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $actividad = Actividad::where('id_actividad', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        if ($actividad->id_evento_google) {
            $this->calendario->eliminarEvento($actividad->id_tenant, $actividad->id_evento_google);
        }

        $actividad->delete();
        return response()->json(['message' => 'Actividad eliminada']);
    }
}
