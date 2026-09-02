<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\MovimientoStock;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::where('id_tenant', $request->user()->id_tenant)
            ->with('categoria')
            ->latest('id_productos');

        // Filtro opcional para búsqueda global (ver shell-layout.component.ts).
        // Sigue devolviendo el arreglo completo sin paginar a propósito: este
        // mismo endpoint lo consumen también el catálogo POS y los terminales
        // de hotel/restaurante/farmacia, que necesitan la lista entera.
        $productos->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($qq) use ($request) {
                $qq->whereLike('nombre', '%' . $request->search . '%')
                   ->orWhereLike('sku', '%' . $request->search . '%');
            });
        });

        return response()->json($productos->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'id_categorias' => 'required|exists:categorias,id_categoria',
            'descripcion' => 'nullable|string|max:350',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sku' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
<<<<<<< HEAD
            'controla_stock' => 'sometimes|boolean',
=======
            'controla_stock' => 'nullable|boolean',
>>>>>>> 4adda57d9da8fedd6f4a36ddfd3dc430167b4a0a
            'precio_compra' => 'nullable|numeric|min:0',
            'precio' => 'required|numeric|min:0',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        return response()->json(Producto::create($data)->load('categoria'), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Producto::where('id_tenant', $request->user()->id_tenant)->with('categoria')->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $item = Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'id_categorias' => 'sometimes|exists:categorias,id_categoria',
            'descripcion' => 'nullable|string|max:350',
            'sku' => 'nullable|string|max:100',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'controla_stock' => 'sometimes|boolean',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio' => 'sometimes|numeric|min:0',
        ]);

        $item->update($data);

        return response()->json($item->load('categoria'));
    }

    public function destroy(Request $request, string $id)
    {
        $item = Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    public function ajustarStock(Request $request, string $id)
    {
        $item = Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'cantidad' => 'required|integer|not_in:0',
            'motivo' => 'required|string|max:30',
            'operacion' => 'nullable|string|max:30',
        ]);

        $nuevoStock = $item->stock + $data['cantidad'];

        if ($nuevoStock < 0) {
            return response()->json(['message' => 'El ajuste dejaría el stock en negativo'], 422);
        }

        $item->update(['stock' => $nuevoStock]);

        MovimientoStock::create([
            'id_tenant' => $request->user()->id_tenant,
            'id_producto' => $item->id_productos,
            'tipo' => 'ajuste',
            'cantidad' => abs($data['cantidad']),
            'motivo' => $data['motivo'],
            'operacion' => $data['operacion'] ?? null,
            'referencia' => null,
            'stock_resultante' => $nuevoStock,
        ]);

        return response()->json($item->load('categoria'));
    }

    public function movimientos(Request $request, string $id)
    {
        Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        return response()->json(
            MovimientoStock::where('id_tenant', $request->user()->id_tenant)
                ->where('id_producto', $id)
                ->latest()
                ->get()
        );
    }

    public function papelera(Request $request)
    {
        return response()->json(
            Producto::onlyTrashed()
                ->where('id_tenant', $request->user()->id_tenant)
                ->with('categoria')
                ->latest('deleted_at')
                ->get()
        );
    }

    public function restaurar(Request $request, string $id)
    {
        $item = Producto::onlyTrashed()->where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $item->restore();

        return response()->json($item->load('categoria'));
    }

    public function subirFoto(Request $request, string $id)
    {
        $item = Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($item->imagen) {
            Storage::disk('public')->delete($item->imagen);
        }

        $item->update([
            'imagen' => $request->file('imagen')->store('productos', 'public'),
        ]);

        return response()->json($item->load('categoria'));
    }

    public function eliminarFoto(Request $request, string $id)
    {
        $item = Producto::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($item->imagen) {
            Storage::disk('public')->delete($item->imagen);
        }

        $item->update(['imagen' => null]);

        return response()->json($item->load('categoria'));
    }
}
