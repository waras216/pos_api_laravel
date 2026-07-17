<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Proyecto;
use App\Models\Erp\ProyectoHora;
use Illuminate\Http\Request;

class ProyectoHoraController extends Controller
{
    private function proyectoDelTenant(Request $request, string $idProyecto): Proyecto
    {
        return Proyecto::where('id_tenant', $request->user()->id_tenant)->findOrFail($idProyecto);
    }

    public function index(Request $request, string $idProyecto)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);

        return response()->json(
            $proyecto->registrosHoras()->latest('fecha')->get()
        );
    }

    public function store(Request $request, string $idProyecto)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);

        $data = $request->validate([
            'colaborador' => 'required|string|max:150',
            'fecha' => 'required|date',
            'horas' => 'required|numeric|min:0.25|max:24',
            'descripcion' => 'nullable|string|max:200',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_proyecto'] = $proyecto->id;

        return response()->json(ProyectoHora::create($data), 201);
    }

    public function destroy(Request $request, string $idProyecto, string $id)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);
        $registro = ProyectoHora::where('id_proyecto', $proyecto->id)->findOrFail($id);
        $registro->delete();

        return response()->json(['message' => 'Registro eliminado']);
    }
}
