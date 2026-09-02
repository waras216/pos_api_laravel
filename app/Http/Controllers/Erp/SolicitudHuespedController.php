<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Habitacion;
use App\Models\Erp\SolicitudHuesped;
use Illuminate\Http\Request;

class SolicitudHuespedController extends Controller
{
    public function index(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $query = SolicitudHuesped::where('id_tenant', $idTenant)
            ->with('habitacion:id,numero,tipo');

        if ($request->query('estado')) {
            $query->where('estado', $request->query('estado'));
        }

        return response()->json(
            $query->orderByRaw("estado = 'resuelta' asc")->latest()->get()
        );
    }

    public function store(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $habitacion = Habitacion::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'categoria' => 'sometimes|in:queja,solicitud,otro',
            'prioridad' => 'sometimes|in:baja,media,alta',
        ]);

        $solicitud = SolicitudHuesped::create([
            'id_tenant' => $idTenant,
            'id_habitacion' => $habitacion->id,
            'huesped' => $habitacion->huesped,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'categoria' => $data['categoria'] ?? 'solicitud',
            'prioridad' => $data['prioridad'] ?? 'media',
            'estado' => 'abierta',
        ]);

        return response()->json($solicitud->load('habitacion:id,numero,tipo'), 201);
    }

    public function cambiarEstado(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $solicitud = SolicitudHuesped::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'estado' => 'required|in:abierta,en_progreso,resuelta',
        ]);

        $solicitud->update([
            'estado' => $data['estado'],
            'resuelta_at' => $data['estado'] === 'resuelta' ? now() : null,
        ]);

        return response()->json($solicitud->load('habitacion:id,numero,tipo'));
    }
}
