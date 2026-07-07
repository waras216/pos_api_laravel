<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Envio;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Envio::where('id_tenant', $request->user()->id_tenant)
                ->latest('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'destino' => 'required|string|max:150',
            'transportista' => 'required|string|max:100',
            'eta' => 'required|string|max:100',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['estado'] = 'en_transito';

        return response()->json(Envio::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Envio::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $envio = Envio::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'destino' => 'sometimes|string|max:150',
            'transportista' => 'sometimes|string|max:100',
            'eta' => 'sometimes|string|max:100',
            'estado' => 'sometimes|in:en_transito,entregado',
        ]);

        $envio->update($data);

        return response()->json($envio);
    }

    public function destroy(Request $request, string $id)
    {
        $envio = Envio::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $envio->delete();

        return response()->json(['message' => 'Envio eliminado']);
    }
}
