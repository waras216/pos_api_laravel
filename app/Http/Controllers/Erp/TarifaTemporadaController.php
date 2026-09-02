<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\TarifaTemporada;
use Illuminate\Http\Request;

class TarifaTemporadaController extends Controller
{
    public function index(Request $request)
    {
        $tarifas = TarifaTemporada::where('id_tenant', $request->user()->id_tenant)
            ->orderBy('fecha_inicio')
            ->get();

        return response()->json($tarifas);
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_ajuste' => 'required|in:porcentaje,monto_fijo',
            'valor' => 'required|numeric',
        ]);

        if ($this->hayTraslape($idTenant, $data['fecha_inicio'], $data['fecha_fin'])) {
            return response()->json(['message' => 'Ya existe una temporada que se cruza con esas fechas.'], 422);
        }

        $data['id_tenant'] = $idTenant;
        $tarifa = TarifaTemporada::create($data);

        return response()->json($tarifa, 201);
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $tarifa = TarifaTemporada::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
            'tipo_ajuste' => 'sometimes|in:porcentaje,monto_fijo',
            'valor' => 'sometimes|numeric',
        ]);

        $desde = $data['fecha_inicio'] ?? $tarifa->fecha_inicio->toDateString();
        $hasta = $data['fecha_fin'] ?? $tarifa->fecha_fin->toDateString();

        if ($this->hayTraslape($idTenant, $desde, $hasta, $tarifa->id)) {
            return response()->json(['message' => 'Ya existe una temporada que se cruza con esas fechas.'], 422);
        }

        $tarifa->update($data);

        return response()->json($tarifa);
    }

    public function destroy(Request $request, string $id)
    {
        $tarifa = TarifaTemporada::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $tarifa->delete();

        return response()->json(['message' => 'Temporada eliminada']);
    }

    private function hayTraslape(int $idTenant, string $desde, string $hasta, ?int $ignorarId = null): bool
    {
        return TarifaTemporada::where('id_tenant', $idTenant)
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->where('fecha_inicio', '<=', $hasta)
            ->where('fecha_fin', '>=', $desde)
            ->exists();
    }
}
