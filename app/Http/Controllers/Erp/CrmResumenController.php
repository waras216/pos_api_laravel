<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Cliente;
use App\Models\Oportunidad;
use Illuminate\Http\Request;

class CrmResumenController extends Controller
{
    public function resumen(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        return response()->json([
            'contactos' => Cliente::where('id_tenant', $idTenant)->count(),
            'oportunidades' => Oportunidad::where('id_tenant', $idTenant)->whereNotIn('estado', ['ganada', 'perdida'])->count(),
            'ticketsAbiertos' => Actividad::where('id_tenant', $idTenant)->where('estado', 'pendiente')->count(),
        ]);
    }

    public function interacciones(Request $request)
    {
        $interacciones = Actividad::where('id_tenant', $request->user()->id_tenant)
            ->with('cliente')
            ->latest('fecha_inicio')
            ->take(10)
            ->get()
            ->map(fn ($a) => [
                'cliente' => $a->cliente->nombre ?? 'Sin cliente',
                'tipo' => $a->tipo,
                'asunto' => $a->titulo,
                'fecha' => optional($a->fecha_inicio)->diffForHumans() ?? $a->created_at->diffForHumans(),
            ]);

        return response()->json($interacciones);
    }
}
