<?php

namespace App\Services\Erp;

use App\Models\Erp\TarifaTemporada;
use Carbon\Carbon;

class TarifaTemporadaService
{
    /**
     * Calcula el cargo de hospedaje noche por noche a partir de la fecha de check-in,
     * aplicando la temporada vigente en cada fecha (si existe) sobre el precio base
     * de la habitación.
     *
     * @return array{total: float, detalle: array<int, array{fecha: string, tarifa: float, temporada: string|null}>}
     */
    public function calcularCargoHospedaje(int $idTenant, float $precioBase, string $fechaCheckin, int $noches): array
    {
        if ($noches <= 0) {
            return ['total' => 0.0, 'detalle' => []];
        }

        $temporadas = TarifaTemporada::where('id_tenant', $idTenant)->get();

        $detalle = [];
        $total = 0.0;
        $fecha = Carbon::parse($fechaCheckin);

        for ($i = 0; $i < $noches; $i++) {
            $fechaStr = $fecha->toDateString();
            $temporada = $temporadas->first(
                fn (TarifaTemporada $t) => $fechaStr >= $t->fecha_inicio->toDateString() && $fechaStr <= $t->fecha_fin->toDateString()
            );

            $tarifaNoche = $precioBase;
            if ($temporada) {
                $tarifaNoche = $temporada->tipo_ajuste === 'porcentaje'
                    ? $precioBase * (1 + $temporada->valor / 100)
                    : $precioBase + $temporada->valor;
                $tarifaNoche = max(0, $tarifaNoche);
            }

            $detalle[] = ['fecha' => $fechaStr, 'tarifa' => round($tarifaNoche, 2), 'temporada' => $temporada?->nombre];
            $total += $tarifaNoche;
            $fecha->addDay();
        }

        return ['total' => round($total, 2), 'detalle' => $detalle];
    }
}
