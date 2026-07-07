<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Pedido::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente' => 'required|string|max:150',
            'total' => 'required|numeric|min:0',
            'estado' => 'nullable|in:pendiente,enviado,facturado',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['estado'] = $data['estado'] ?? 'pendiente';
        $data['fecha'] = now()->toDateString();

        return response()->json(Pedido::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Pedido::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $pedido = Pedido::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'cliente' => 'sometimes|string|max:150',
            'total' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:pendiente,enviado,facturado',
        ]);

        $pedido->update($data);

        return response()->json($pedido);
    }

    public function destroy(Request $request, string $id)
    {
        $pedido = Pedido::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado']);
    }
}
