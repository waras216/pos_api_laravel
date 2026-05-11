<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;


class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
          return response()->json(
            Categoria::where('id_tenant', $request->user()->id_tenant)
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request ->validate([
            'id_tenant' => 'required|exists:tenants,id_tenant',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria = Categoria::create($data);

        return response()->json($categoria,201);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string|max:100',
            'activo' => 'sometimes|boolean',
        ]);

        $categoria->update($data);

        return response()->json($categoria);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return response()->json(['message' => 'Categoria eliminada']);
    }
}
