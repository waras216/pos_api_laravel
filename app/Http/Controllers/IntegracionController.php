<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use Illuminate\Http\Request;

class IntegracionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Integracion::where('id_tenant', $request->user()->id_tenant)
                ->get()
        );
    }

    public function toggle(Request $request, string $id)
    {
        $integracion = Integracion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        $integracion->update([
            'estado' => $integracion->estado === 'conectada' ? 'desconectada' : 'conectada',
        ]);

        return response()->json($integracion);
    }
}
