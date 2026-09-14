<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Erp\MovimientoCredito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
    // Clientes con línea de crédito activa (limite_credito > 0), con su saldo
    // actual -- es el listado principal de la pantalla "Clientes y Créditos".
    public function index(Request $request)
    {
        return response()->json(
            Cliente::where('id_tenant', $request->user()->id_tenant)
                ->where('limite_credito', '>', 0)
                ->orderBy('nombre')
                ->get()
        );
    }

    public function actualizarLimite(Request $request, string $idCliente)
    {
        $cliente = Cliente::where('id_tenant', $request->user()->id_tenant)->findOrFail($idCliente);

        $data = $request->validate(['limite_credito' => 'required|numeric|min:0']);

        $cliente->update(['limite_credito' => $data['limite_credito']]);

        return response()->json($cliente);
    }

    public function movimientos(Request $request, string $idCliente)
    {
        $idTenant = $request->user()->id_tenant;
        Cliente::where('id_tenant', $idTenant)->findOrFail($idCliente);

        return response()->json(
            MovimientoCredito::where('id_tenant', $idTenant)
                ->where('id_cliente', $idCliente)
                ->latest('fecha')
                ->latest('id')
                ->get()
        );
    }

    public function cargar(Request $request, string $idCliente)
    {
        return $this->registrarMovimiento($request, $idCliente, 'cargo');
    }

    public function abonar(Request $request, string $idCliente)
    {
        return $this->registrarMovimiento($request, $idCliente, 'abono');
    }

    private function registrarMovimiento(Request $request, string $idCliente, string $tipo)
    {
        $idTenant = $request->user()->id_tenant;
        $cliente = Cliente::where('id_tenant', $idTenant)->findOrFail($idCliente);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:150',
            'notas' => 'nullable|string|max:350',
        ]);

        if ($tipo === 'cargo' && $cliente->saldo_credito + $data['monto'] > $cliente->limite_credito) {
            return response()->json(['message' => 'El cargo excede el límite de crédito del cliente.'], 422);
        }

        $movimiento = DB::transaction(function () use ($cliente, $idTenant, $tipo, $data, $request) {
            $nuevoSaldo = $tipo === 'cargo'
                ? $cliente->saldo_credito + $data['monto']
                : max(0, $cliente->saldo_credito - $data['monto']);

            $cliente->update(['saldo_credito' => $nuevoSaldo]);

            return MovimientoCredito::create([
                'id_tenant' => $idTenant,
                'id_cliente' => $cliente->id_cliente,
                'id_usuario' => $request->user()->id_usuario,
                'tipo' => $tipo,
                'monto' => $data['monto'],
                'saldo_resultante' => $nuevoSaldo,
                'referencia' => $data['referencia'] ?? null,
                'notas' => $data['notas'] ?? null,
                'fecha' => now()->toDateString(),
            ]);
        });

        return response()->json($movimiento, 201);
    }
}
