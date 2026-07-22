<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Oportunidad;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lead = Lead::where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'usuario']);


        $lead->when($request->filled('search'), function ($q) use($request){
            $q->whereLike('titulo', '%' . $request->search . '%');
        });

        $lead->when($request->filled('estado') && $request->estado !== 'todos', function ($q) use ($request){
            $q->where('estado', $request->estado);
        });
        
        $lead->latest('id_lead');

        $perPage = min((int) $request->input('per_page', 15), 500);
        return response()->json($lead->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
                'id_cliente' => 'nullable|exists:clientes,id_cliente',
                'titulo' => 'required|string|max:150',
                'nombre' => 'nullable|string|max:150',
                'email' => 'nullable|email|max:200',
                'telefono' => 'nullable|string|max:20',
                'descripcion' => 'nullable|string',
                'estado' => 'sometimes|in:nuevo,contactado,calificado,perdido,convertido',
                'fuente' => 'sometimes|in:web,referido,llamada,email,otro',
                'valor_estimado' => 'nullable|numeric|min:0',
            ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;

        return response()->json(Lead::create($data),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Lead::where('id_lead', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'usuario', 'actividades'])
            ->firstOrFail()
        );        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lead = Lead::where('id_lead', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $data = $request->validate([
            'id_cliente'     => 'nullable|exists:clientes,id_cliente',
            'titulo'         => 'sometimes|string|max:150',
            'nombre'         => 'nullable|string|max:150',
            'email'          => 'nullable|email|max:200',
            'telefono'       => 'nullable|string|max:20',
            'descripcion'    => 'nullable|string',
            'estado'         => 'sometimes|in:nuevo,contactado,calificado,perdido,convertido',
            'fuente'         => 'sometimes|in:web,referido,llamada,email,otro',
            'valor_estimado' => 'nullable|numeric|min:0',
        ]);

        $lead->update($data);
        return response()->json($lead);
    }

    /**
     * Convert this lead into a Cliente + Oportunidad.
     */
    public function convertir(Request $request, string $id)
    {
        $data = $request->validate([
            'id_pipeline' => [
                'required',
                Rule::exists('pipelines', 'id_pipeline')->where('id_tenant', $request->user()->id_tenant),
            ],
            'etapa'   => 'sometimes|in:prospeccion,contacto,propuesta,negociacion,cierre',
            'valor'   => 'nullable|numeric|min:0',
            'nombre'  => 'nullable|string|max:150',
            'email'   => 'nullable|email|max:200',
            'telefono'=> 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:150',
            'tipo'    => 'sometimes|in:persona,empresa',
        ]);

        return DB::transaction(function () use ($request, $id, $data) {
            $lead = Lead::where('id_lead', $id)
                ->where('id_tenant', $request->user()->id_tenant)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lead->estado === 'convertido') {
                abort(422, 'Este lead ya fue convertido.');
            }

            $nombre = $data['nombre'] ?? $lead->nombre;
            if (!$lead->id_cliente && empty($nombre)) {
                abort(422, 'El nombre es requerido para crear el cliente.');
            }

            if ($lead->id_cliente) {
                $cliente = Cliente::where('id_cliente', $lead->id_cliente)
                    ->where('id_tenant', $request->user()->id_tenant)
                    ->firstOrFail();
            } else {
                $cliente = Cliente::create([
                    'id_tenant' => $request->user()->id_tenant,
                    'nombre'    => $nombre,
                    'email'     => $data['email'] ?? $lead->email,
                    'telefono'  => $data['telefono'] ?? $lead->telefono,
                    'empresa'   => $data['empresa'] ?? null,
                    'tipo'      => $data['tipo'] ?? 'persona',
                ]);
                $lead->id_cliente = $cliente->id_cliente;
            }

            $oportunidad = Oportunidad::create([
                'id_tenant'   => $request->user()->id_tenant,
                'id_cliente'  => $cliente->id_cliente,
                'id_pipeline' => $data['id_pipeline'],
                'id_usuario'  => $request->user()->id_usuario,
                'titulo'      => $lead->titulo,
                'valor'       => $data['valor'] ?? $lead->valor_estimado ?? 0,
                'etapa'       => $data['etapa'] ?? 'prospeccion',
            ]);

            Actividad::create([
                'id_tenant'      => $request->user()->id_tenant,
                'id_usuario'     => $request->user()->id_usuario,
                'id_cliente'     => $cliente->id_cliente,
                'id_lead'        => $lead->id_lead,
                'id_oportunidad' => $oportunidad->id_oportunidad,
                'tipo'           => 'nota',
                'titulo'         => 'Lead convertido a oportunidad',
                'estado'         => 'completada',
            ]);

            $lead->estado = 'convertido';
            $lead->save();

            return response()->json([
                'lead'        => $lead->fresh(['cliente']),
                'cliente'     => $cliente,
                'oportunidad' => $oportunidad->load(['cliente', 'pipeline']),
            ], 201);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $lead = Lead::where('id_lead', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $lead->delete();
        return response()->json(['message' => 'Lead eliminado']);
    }
}
