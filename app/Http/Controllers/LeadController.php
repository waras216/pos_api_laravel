<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lead = Lead::where('id_tenant', $request->user()->id_tenant)
            ->with(['cliente', 'usuario']);


        $lead->when($request->filled('buscar'), function ($q) use($request){
            $q->where('titulo', 'like', '%' . $request->search . '%');
        });

        $lead->when($request->filled('filterEstatus') && $request->filterEstatus !== 'todos', function ($q) use ($request){
            $q->where('estado', $request->filterEstatus);
        });
        
        $lead->latest('id_lead');

        return response()->json($lead->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
                'id_cliente' => 'nullable|exists:clientes,id_cliente',
                'titulo' => 'required|string|max:150',
                'descripcion' => 'nullable|string',
                'estado' => 'sometimes|in:nuevo,contactado, calificado, perdido',
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
            'descripcion'    => 'nullable|string',
            'estado'         => 'sometimes|in:nuevo,contactado,calificado,perdido',
            'fuente'         => 'sometimes|in:web,referido,llamada,email,otro',
            'valor_estimado' => 'nullable|numeric|min:0',
        ]);

        $lead->update($data);
        return response()->json($lead);
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
