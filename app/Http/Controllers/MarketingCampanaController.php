<?php

namespace App\Http\Controllers;

use App\Models\CampanaMarketing;
use Illuminate\Http\Request;

class MarketingCampanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            CampanaMarketing::where('id_tenant', $request->user()->id_tenant)
                ->latest()
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_compania' => 'required|string|max:150',
            'segmento' => 'required|string|max:150',
            'estado' => 'sometimes|in:activa,pausada,finalizada',
            'fecha_inicio' => 'nullable|date',
            'lista_contactos' => 'sometimes|array',
            'lista_contactos.*.nombre' => 'required_with:lista_contactos|string|max:150',
            'lista_contactos.*.email' => 'nullable|email|max:200',
            'lista_contactos.*.telefono' => 'nullable|string|max:20',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;

        return response()->json(CampanaMarketing::create($data), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        return response()->json(
            CampanaMarketing::where('id', $id)
                ->where('id_tenant', $request->user()->id_tenant)
                ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $campana = CampanaMarketing::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $data = $request->validate([
            'nombre_compania' => 'sometimes|string|max:150',
            'segmento' => 'sometimes|string|max:150',
            'estado' => 'sometimes|in:activa,pausada,finalizada',
            'fecha_inicio' => 'nullable|date',
            'lista_contactos' => 'sometimes|array',
            'lista_contactos.*.nombre' => 'required_with:lista_contactos|string|max:150',
            'lista_contactos.*.email' => 'nullable|email|max:200',
            'lista_contactos.*.telefono' => 'nullable|string|max:20',
        ]);

        $campana->update($data);

        return response()->json($campana);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $campana = CampanaMarketing::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $campana->delete();

        return response()->json(['message' => 'campaña eliminada']);
    }
}
