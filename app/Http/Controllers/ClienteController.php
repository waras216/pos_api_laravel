<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Cliente::where('id_tenant', $request->user()->id_tenant)
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'apellido_p' => 'nullable|string|max:150',
            'apellido_m' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:150',
            'sector_empresarial' => 'nullable|string|max:100',
            'rfc' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'tipo' => 'sometimes|in:persona,empresa',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        return response()->json(Cliente::create($data), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Cliente::where('id_cliente', $id)
                ->where('id_tenant', $request->user()->id_tenant)
                ->with(['contactos', 'leads', 'oportunidades'])
                ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::where('id_cliente', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'apellido_p' => 'nullable|string|max:150',
            'apellido_m' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:150',
            'sector_empresarial' => 'nullable|string|max:100',
            'rfc' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'tipo'  => 'sometimes|in:persona,empresa',
            'activo' => 'sometimes|boolean',
        ]);            

        $cliente->update($data);
        return response()->json($cliente);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $cliente = Cliente::where('id_cliente', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $cliente->delete();
        return response()->json(['message' => 'cliente Eliminado Correctamente']);
    }
}
