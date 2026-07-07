<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\OrdenCompra;
use Illuminate\Http\Request;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            OrdenCompra::where('id_tenant', $request->user()->id_tenant)
                ->latest('fecha')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor' => 'required|string|max:150',
            'items' => 'required|integer|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['fecha'] = now()->toDateString();
        $data['estado'] = 'pendiente';

        return response()->json(OrdenCompra::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function destroy(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $orden->delete();

        return response()->json(['message' => 'Orden eliminada']);
    }

    public function recibir(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $orden->update(['estado' => 'recibida']);

        return response()->json($orden);
    }

    public function cancelar(Request $request, string $id)
    {
        $orden = OrdenCompra::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $orden->update(['estado' => 'cancelada']);

        return response()->json($orden);
    }
}
