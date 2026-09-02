<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Listado de todos los tenants registrados en la plataforma (solo superadmin).
     */
    public function index()
    {
        $tenants = Tenant::with(['negocio', 'plan'])
            ->withCount(['miembros as usuarios_count'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($tenants->map(fn (Tenant $tenant) => $this->presentResumen($tenant)));
    }

    /**
     * KPI de usuarios registrados agrupado por nicho de negocio (solo superadmin).
     */
    public function kpisPorNicho()
    {
        $kpis = Tenant::with('negocio')
            ->withCount(['miembros as usuarios_count'])
            ->get()
            ->groupBy(fn (Tenant $tenant) => optional($tenant->negocio)->id_tiponegocio ?? 0)
            ->map(function ($grupo) {
                $negocio = $grupo->first()->negocio;

                return [
                    'id_tiponegocio' => optional($negocio)->id_tiponegocio,
                    'nicho' => optional($negocio)->nombre_negocio ?? 'Sin nicho',
                    'empresas' => $grupo->count(),
                    'usuarios' => $grupo->sum('usuarios_count'),
                ];
            })
            ->sortByDesc('usuarios')
            ->values();

        return response()->json($kpis);
    }

    /**
     * Detalle de un tenant: datos generales + equipo (miembros activos).
     */
    public function show(string $id)
    {
        $tenant = Tenant::with(['negocio', 'plan'])->findOrFail($id);

        return response()->json(array_merge($this->presentResumen($tenant), [
            'miembros' => $tenant->membresias()
                ->where('membresias.estado', 'activa')
                ->join('usuarios', 'usuarios.id_usuario', '=', 'membresias.id_usuario')
                ->orderBy('usuarios.nombre')
                ->get([
                    'usuarios.id_usuario',
                    'usuarios.nombre',
                    'usuarios.email',
                    'membresias.es_owner',
                ]),
        ]));
    }

    /**
     * Suspender/reactivar un tenant y/o reasignarle un plan (solo superadmin).
     */
    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        $data = $request->validate([
            'estado' => 'sometimes|string|in:activo,suspendido',
            'id_plan' => 'sometimes|integer|exists:plans,id_plan',
            'modulo_crm' => 'sometimes|boolean',
            'modulo_pos' => 'sometimes|boolean',
            'modulo_erp' => 'sometimes|boolean',
        ]);

        $tenant->update($data);

        return response()->json($this->presentResumen($tenant->fresh(['negocio', 'plan'])));
    }

    /**
     * Elimina (soft-delete) un tenant. No borra sus datos, solo lo saca del listado activo.
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return response()->json(['message' => 'Empresa eliminada correctamente']);
    }

    private function presentResumen(Tenant $tenant): array
    {
        return [
            'id_tenant' => $tenant->id_tenant,
            'empresa' => $tenant->nombre_tenant,
            'subdominio' => $tenant->subdominio,
            'estado' => $tenant->estado,
            'nicho' => optional($tenant->negocio)->nombre_negocio,
            'moneda' => $tenant->moneda,
            'modulos' => [
                'crm' => (bool) $tenant->modulo_crm,
                'pos' => (bool) $tenant->modulo_pos,
                'erp' => (bool) $tenant->modulo_erp,
            ],
            'id_plan' => $tenant->id_plan,
            'plan' => optional($tenant->plan)->nombre_plan,
            'usuarios' => $tenant->usuarios_count ?? $tenant->membresias()->where('estado', 'activa')->count(),
            'onboardingCompleto' => (bool) $tenant->onboarding_completado,
            'creadoEn' => $tenant->created_at,
        ];
    }
}
