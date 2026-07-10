<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Producto::where('id_tenant',$request->user()->id_tenant)
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_categorias' => 'required|exists:categorias,id_categoria',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:350',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        return response()->json(Producto::create($data),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        return response()->json(
            Producto::where('id_tenant', $request->user()->id_tenant)
            ->with('categoria')
            ->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::where('id_productos', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $data = $request->validate([
            'id_categorias' => 'sometimes|exists:categorias,id_categoria',
            'nombre' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string|max:350',
            'precio' => 'sometimes|numeric|min:0',
            'precio_compra' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'activo' => 'sometimes|boolean',
        ]);

        $producto->update($data);

        return response()->json($producto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $producto = Producto::where('id_productos', $id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();
        
        $producto->delete();

        return response()->json(['message'=>'Producto eliminado']);
    }
}
