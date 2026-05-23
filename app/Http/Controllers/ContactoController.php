<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json([
            Contacto::where('id_tenant', $request->user()->id_tenant)
            ->with('cliente')
            ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'nombre' => 'required|string|max:150',
            'apellido_p' => 'nullable|string|max:150',
            'apellido_m' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'principal' => 'sometimes|boolean',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        return response()->json(Contacto::create($data), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Contacto::where('id_contacto', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->with('clientes')
            ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contacto = Contacto::where('id_contacto',$id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();
        $data = $request ->validate([
            'nombre' => 'sometimes|string|max:150',
            'apellido_p' => 'nullable|string|max:150',
            'apellido_m' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:150',
            'principal' => 'sometimes|boolean',
        ]);

        $contacto->update($data);
        return response()->json($contacto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $contacto = Contacto::where('id_contacto', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $contacto->delete();
        return response()->json(['message' => 'Contacto eliminado']);
    }
}
