<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $roles = Rol::where('id_tenant', $idTenant)
            ->with(['permisos:id_permiso,clave', 'usuarios' => fn ($q) => $q->where('usuario_rol.id_tenant', $idTenant)->select('usuarios.id_usuario')])
            ->orderByDesc('es_sistema')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($r) => [
                'id_rol' => $r->id_rol,
                'clave' => $r->clave,
                'nombre' => $r->nombre,
                'descripcion' => $r->descripcion,
                'es_sistema' => $r->es_sistema,
                'usuarios_count' => $r->usuarios->count(),
                'usuarios_ids' => $r->usuarios->pluck('id_usuario'),
                'permisos' => $r->permisos->pluck('clave'),
            ]);

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'required|array',
            'permisos.*' => 'string|exists:permisos,clave',
        ]);

        $clave = 'custom.' . Str::slug($data['nombre']) . '.' . Str::random(6);

        $rol = Rol::create([
            'id_tenant' => $idTenant,
            'id_modulo' => null,
            'clave' => $clave,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'es_sistema' => false,
        ]);

        $idsPermisos = \App\Models\Permiso::whereIn('clave', $data['permisos'])->pluck('id_permiso');
        $rol->permisos()->sync($idsPermisos);

        return response()->json($this->presentar($rol->fresh('permisos')), 201);
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $rol = Rol::where('id_tenant', $idTenant)->findOrFail($id);

        if ($rol->es_sistema) {
            return response()->json(['message' => 'Los roles de sistema (Administrador, Miembro) no se pueden editar.'], 422);
        }

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'sometimes|array',
            'permisos.*' => 'string|exists:permisos,clave',
        ]);

        $rol->update([
            'nombre' => $data['nombre'] ?? $rol->nombre,
            'descripcion' => $data['descripcion'] ?? $rol->descripcion,
        ]);

        if (isset($data['permisos'])) {
            $idsPermisos = \App\Models\Permiso::whereIn('clave', $data['permisos'])->pluck('id_permiso');
            $rol->permisos()->sync($idsPermisos);
        }

        return response()->json($this->presentar($rol->fresh('permisos')));
    }

    public function destroy(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $rol = Rol::where('id_tenant', $idTenant)->findOrFail($id);

        if ($rol->es_sistema) {
            return response()->json(['message' => 'Los roles de sistema no se pueden eliminar.'], 422);
        }

        $usuariosAsignados = \Illuminate\Support\Facades\DB::table('usuario_rol')
            ->where('id_rol', $rol->id_rol)->where('id_tenant', $idTenant)->count();

        if ($usuariosAsignados > 0) {
            return response()->json(['message' => 'No puedes eliminar un rol que todavía tiene miembros asignados.'], 422);
        }

        $rol->delete();

        return response()->json(['message' => 'Rol eliminado']);
    }

    public function asignarUsuario(Request $request, string $id, string $idUsuario)
    {
        $idTenant = $request->user()->id_tenant;
        $rol = Rol::where('id_tenant', $idTenant)->findOrFail($id);

        Membresia::where('id_usuario', $idUsuario)->where('id_tenant', $idTenant)->firstOrFail();

        \Illuminate\Support\Facades\DB::table('usuario_rol')->updateOrInsert(
            ['id_usuario' => $idUsuario, 'id_tenant' => $idTenant, 'id_rol' => $rol->id_rol],
            ['asignado_por' => $request->user()->id_usuario, 'asignado_en' => now(), 'created_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['message' => 'Rol asignado']);
    }

    public function quitarUsuario(Request $request, string $id, string $idUsuario)
    {
        $idTenant = $request->user()->id_tenant;
        $rol = Rol::where('id_tenant', $idTenant)->findOrFail($id);

        \Illuminate\Support\Facades\DB::table('usuario_rol')
            ->where('id_usuario', $idUsuario)
            ->where('id_tenant', $idTenant)
            ->where('id_rol', $rol->id_rol)
            ->delete();

        return response()->json(['message' => 'Rol removido']);
    }

    private function presentar(Rol $rol): array
    {
        return [
            'id_rol' => $rol->id_rol,
            'clave' => $rol->clave,
            'nombre' => $rol->nombre,
            'descripcion' => $rol->descripcion,
            'es_sistema' => $rol->es_sistema,
            'permisos' => $rol->permisos->pluck('clave'),
        ];
    }
}
