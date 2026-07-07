<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Inventario::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'sku' => 'required|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        return response()->json(Inventario::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Inventario::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $item = Inventario::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'sku' => 'sometimes|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Request $request, string $id)
    {
        $item = Inventario::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
