<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Proyecto;
use App\Models\Erp\ProyectoTarea;
use App\Models\Notificacion;
use App\Models\Rol;
use Illuminate\Http\Request;

class ProyectoTareaController extends Controller
{
    private function proyectoDelTenant(Request $request, string $idProyecto): Proyecto
    {
        return Proyecto::where('id_tenant', $request->user()->id_tenant)->findOrFail($idProyecto);
    }

    public function index(Request $request, string $idProyecto)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);

        return response()->json($proyecto->tareas);
    }

    public function store(Request $request, string $idProyecto)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);

        $data = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'asignado' => 'nullable|string|max:150',
            'estado' => 'sometimes|in:pendiente,en_progreso,completada',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_proyecto'] = $proyecto->id;
        $data['estado'] = $data['estado'] ?? 'pendiente';
        $data['orden'] = ProyectoTarea::where('id_proyecto', $proyecto->id)
            ->where('estado', $data['estado'])
            ->count();

        return response()->json(ProyectoTarea::create($data), 201);
    }

    public function update(Request $request, string $idProyecto, string $id)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);
        $tarea = ProyectoTarea::where('id_proyecto', $proyecto->id)->findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'asignado' => 'nullable|string|max:150',
            'estado' => 'sometimes|in:pendiente,en_progreso,completada',
        ]);

        $eraCompletada = $tarea->estado === 'completada';
        $tarea->update($data);

        if (($data['estado'] ?? null) === 'completada' && ! $eraCompletada) {
            $this->notificarTareaCompletada($request, $proyecto, $tarea);
        }

        return response()->json($tarea);
    }

    private function notificarTareaCompletada(Request $request, Proyecto $proyecto, ProyectoTarea $tarea): void
    {
        $idsAdmin = Rol::idsAdminTenant($request->user()->id_tenant);

        foreach ($idsAdmin as $idUsuario) {
            Notificacion::create([
                'id_tenant' => $request->user()->id_tenant,
                'id_usuario' => $idUsuario,
                'titulo' => '✅ Tarea completada',
                'mensaje' => sprintf('"%s" del proyecto "%s" fue marcada como completada.', $tarea->titulo, $proyecto->nombre),
                'tipo' => 'success',
                'url' => '/erp',
            ]);
        }
    }

    public function destroy(Request $request, string $idProyecto, string $id)
    {
        $proyecto = $this->proyectoDelTenant($request, $idProyecto);
        $tarea = ProyectoTarea::where('id_proyecto', $proyecto->id)->findOrFail($id);
        $tarea->delete();

        return response()->json(['message' => 'Tarea eliminada']);
    }
}
