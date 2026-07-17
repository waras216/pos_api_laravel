<?php

namespace App\Http\Controllers;

use App\Models\Permiso;

class PermisoController extends Controller
{
    /**
     * Catálogo completo de permisos, agrupados por recurso (la parte antes
     * del "." en la clave, ej. "leads.eliminar" -> recurso "leads").
     */
    public function index()
    {
        $permisos = Permiso::orderBy('clave')->get(['id_permiso', 'clave', 'descripcion']);

        $agrupado = $permisos->groupBy(fn ($p) => explode('.', $p->clave)[0])
            ->map(fn ($grupo) => $grupo->map(fn ($p) => [
                'id_permiso' => $p->id_permiso,
                'clave' => $p->clave,
                'accion' => explode('.', $p->clave)[1] ?? null,
                'descripcion' => $p->descripcion,
            ])->values());

        return response()->json($agrupado);
    }
}
