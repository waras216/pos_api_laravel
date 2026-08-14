<?php

namespace App\Console\Commands;

use App\Models\Actividad;
use App\Services\Crm\AutomatizacionEngine;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('automatizaciones:actividades-vencidas')]
#[Description('Dispara la automatización "actividad_vencida" del CRM para cada actividad pendiente que ya pasó su fecha límite (una sola vez por actividad).')]
class DispararAutomatizacionesActividadesVencidas extends Command
{
    public function handle(AutomatizacionEngine $automatizaciones): int
    {
        $vencidas = Actividad::where('estado', '!=', 'completada')
            ->where('automatizacion_disparada', false)
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now())
            ->get();

        foreach ($vencidas as $actividad) {
            $automatizaciones->disparar('actividad_vencida', $actividad->id_tenant, ['actividad' => $actividad]);
            $actividad->update(['automatizacion_disparada' => true]);
        }

        $this->info("Procesadas {$vencidas->count()} actividad(es) vencida(s).");

        return self::SUCCESS;
    }
}
