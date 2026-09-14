<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Transferencia;
use App\Models\Erp\TransferenciaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransferenciaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Transferencia::where('id_tenant', $request->user()->id_tenant)
                ->with(['sucursalOrigen', 'sucursalDestino', 'usuario', 'items.producto'])
                ->latest('fecha')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_sucursal_origen' => ['required', Rule::exists('erp_sucursales', 'id_sucursal')->where('id_tenant', $idTenant)],
            'id_sucursal_destino' => ['required', 'different:id_sucursal_origen', Rule::exists('erp_sucursales', 'id_sucursal')->where('id_tenant', $idTenant)],
            'notas' => 'nullable|string|max:350',
            'items' => 'required|array|min:1',
            'items.*.id_producto' => ['required', Rule::exists('productos', 'id_productos')->where('id_tenant', $idTenant)],
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $transferencia = DB::transaction(function () use ($data, $idTenant, $request) {
            $transferencia = Transferencia::create([
                'id_tenant' => $idTenant,
                'id_sucursal_origen' => $data['id_sucursal_origen'],
                'id_sucursal_destino' => $data['id_sucursal_destino'],
                'id_usuario' => $request->user()->id_usuario,
                'fecha' => now()->toDateString(),
                'estado' => 'pendiente',
                'notas' => $data['notas'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                TransferenciaItem::create([
                    'id_transferencia' => $transferencia->id,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            return $transferencia;
        });

        return response()->json($transferencia->load(['sucursalOrigen', 'sucursalDestino', 'usuario', 'items.producto']), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Transferencia::where('id_tenant', $request->user()->id_tenant)
                ->with(['sucursalOrigen', 'sucursalDestino', 'usuario', 'items.producto'])
                ->findOrFail($id)
        );
    }

    public function enviar(Request $request, string $id)
    {
        return $this->transicion($request, $id, 'pendiente', 'enviada');
    }

    public function recibir(Request $request, string $id)
    {
        return $this->transicion($request, $id, 'enviada', 'recibida');
    }

    public function cancelar(Request $request, string $id)
    {
        $transferencia = Transferencia::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($transferencia->estado === 'recibida') {
            return response()->json(['message' => 'Una transferencia ya recibida no se puede cancelar.'], 422);
        }

        $transferencia->update(['estado' => 'cancelada']);

        return response()->json($transferencia->load(['sucursalOrigen', 'sucursalDestino', 'usuario', 'items.producto']));
    }

    public function destroy(Request $request, string $id)
    {
        $transferencia = Transferencia::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($transferencia->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden eliminar transferencias pendientes.'], 422);
        }

        $transferencia->delete();

        return response()->json(['message' => 'Transferencia eliminada']);
    }

    private function transicion(Request $request, string $id, string $estadoActual, string $estadoNuevo)
    {
        $transferencia = Transferencia::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($transferencia->estado !== $estadoActual) {
            return response()->json(['message' => "Esta transferencia debe estar en estado \"{$estadoActual}\"."], 422);
        }

        $transferencia->update(['estado' => $estadoNuevo]);

        return response()->json($transferencia->load(['sucursalOrigen', 'sucursalDestino', 'usuario', 'items.producto']));
    }
}
