<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function show(Request $request)
    {
        $tenant = Tenant::with('negocio')
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        return response()->json($this->present($tenant));
    }

    public function completeOnboarding(Request $request)
    {
        $data = $request->validate([
            'empresa' => 'required|string|max:150',
            'nicho' => 'required|string|in:hotel,restaurante,almacen,farmacia,startup,tienda',
            'moneda' => 'required|string|in:MXN,USD,EUR,COP,ARS',
            'modulos' => 'required|array',
            'modulos.crm' => 'required|boolean',
            'modulos.pos' => 'required|boolean',
            'modulos.erp' => 'required|boolean',
            'datos_nicho' => 'nullable|array',
        ]);

        $negocio = Negocio::where('slug', $data['nicho'])->firstOrFail();

        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();
        $tenant->update([
            'nombre_tenant' => $data['empresa'],
            'id_tiponegocio' => $negocio->id_tiponegocio,
            'moneda' => $data['moneda'],
            'modulo_crm' => $data['modulos']['crm'],
            'modulo_pos' => $data['modulos']['pos'],
            'modulo_erp' => $data['modulos']['erp'],
            'datos_nicho' => $data['datos_nicho'] ?? null,
            'onboarding_completado' => true,
        ]);

        return response()->json($this->present($tenant->fresh('negocio')));
    }

    private function present(Tenant $tenant): array
    {
        return [
            'empresa' => $tenant->nombre_tenant,
            'onboardingCompleto' => (bool) $tenant->onboarding_completado,
            'nichoData' => $tenant->onboarding_completado ? array_merge([
                'nicho' => optional($tenant->negocio)->slug,
                'moneda' => $tenant->moneda,
                'modulos' => [
                    'crm' => (bool) $tenant->modulo_crm,
                    'pos' => (bool) $tenant->modulo_pos,
                    'erp' => (bool) $tenant->modulo_erp,
                ],
            ], $tenant->datos_nicho ?? []) : null,
        ];
    }
}
