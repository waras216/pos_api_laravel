<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Tenant;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function __construct(private OnboardingService $onboarding) {}

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
            'datos_nicho.hotelHabitaciones' => 'nullable|integer|min:1|max:200',
            'datos_nicho.hotelTiposHabitacion' => 'nullable|array',
            'datos_nicho.hotelTiposHabitacion.*' => 'string|max:40',
            'datos_nicho.restMesas' => 'nullable|integer|min:1|max:200',
            'fiscal' => 'nullable|array',
            'fiscal.rfc' => 'nullable|string|max:20',
            'fiscal.razonSocial' => 'nullable|string|max:150',
            'fiscal.regimenFiscal' => 'nullable|string|max:5',
            'fiscal.codigoPostal' => 'nullable|string|max:10',
        ]);

        if (! $data['modulos']['crm'] && ! $data['modulos']['pos'] && ! $data['modulos']['erp']) {
            return response()->json(['message' => 'Selecciona al menos un módulo.'], 422);
        }

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
            'rfc_emisor' => $data['fiscal']['rfc'] ?? null,
            'razon_social_emisor' => $data['fiscal']['razonSocial'] ?? null,
            'regimen_fiscal_emisor' => $data['fiscal']['regimenFiscal'] ?? null,
            'codigo_postal_emisor' => $data['fiscal']['codigoPostal'] ?? null,
        ]);

        $this->onboarding->provisionarRecursosPorNicho($tenant, $data['nicho'], $data['datos_nicho'] ?? []);

        return response()->json($this->present($tenant->fresh('negocio')));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sector' => 'nullable|string|max:100',
            'idioma' => 'nullable|string|in:es,en',
            'zonaHoraria' => 'nullable|string|max:60',
            'moneda' => 'nullable|string|in:MXN,USD,EUR,COP,ARS',
            'empresa' => 'nullable|string|max:150',
            'nicho' => 'nullable|string|in:hotel,restaurante,almacen,farmacia,startup,tienda',
            'modulos' => 'nullable|array',
            'modulos.crm' => 'required_with:modulos|boolean',
            'modulos.pos' => 'required_with:modulos|boolean',
            'modulos.erp' => 'required_with:modulos|boolean',
            'fiscal' => 'nullable|array',
            'fiscal.rfc' => 'nullable|string|max:20',
            'fiscal.razonSocial' => 'nullable|string|max:150',
            'fiscal.regimenFiscal' => 'nullable|string|max:5',
            'fiscal.codigoPostal' => 'nullable|string|max:10',
        ]);

        if (isset($data['modulos']) && ! $data['modulos']['crm'] && ! $data['modulos']['pos'] && ! $data['modulos']['erp']) {
            return response()->json(['message' => 'Selecciona al menos un módulo.'], 422);
        }

        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();

        $cambios = [
            'sector' => $data['sector'] ?? $tenant->sector,
            'idioma' => $data['idioma'] ?? $tenant->idioma,
            'zona_horaria' => $data['zonaHoraria'] ?? $tenant->zona_horaria,
            'moneda' => $data['moneda'] ?? $tenant->moneda,
            'nombre_tenant' => $data['empresa'] ?? $tenant->nombre_tenant,
        ];

        if (! empty($data['nicho'])) {
            $negocio = Negocio::where('slug', $data['nicho'])->firstOrFail();
            $cambios['id_tiponegocio'] = $negocio->id_tiponegocio;
        }

        if (isset($data['modulos'])) {
            $cambios['modulo_crm'] = $data['modulos']['crm'];
            $cambios['modulo_pos'] = $data['modulos']['pos'];
            $cambios['modulo_erp'] = $data['modulos']['erp'];
        }

        if (isset($data['fiscal'])) {
            $cambios['rfc_emisor'] = $data['fiscal']['rfc'] ?? $tenant->rfc_emisor;
            $cambios['razon_social_emisor'] = $data['fiscal']['razonSocial'] ?? $tenant->razon_social_emisor;
            $cambios['regimen_fiscal_emisor'] = $data['fiscal']['regimenFiscal'] ?? $tenant->regimen_fiscal_emisor;
            $cambios['codigo_postal_emisor'] = $data['fiscal']['codigoPostal'] ?? $tenant->codigo_postal_emisor;
        }

        $tenant->update($cambios);

        return response()->json($this->present($tenant->fresh('negocio')));
    }

    public function subirLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();

        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        if ($path === false) {
            return response()->json(['message' => 'No se pudo guardar el logo.'], 500);
        }

        $tenant->update(['logo' => $path]);

        return response()->json($this->present($tenant->fresh('negocio')));
    }

    public function eliminarLogo(Request $request)
    {
        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();

        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $tenant->update(['logo' => null]);
        }

        return response()->json($this->present($tenant->fresh('negocio')));
    }

    private function present(Tenant $tenant): array
    {
        return [
            'empresa' => $tenant->nombre_tenant,
            'logo' => $tenant->logo ? Storage::disk('public')->url($tenant->logo) : null,
            'onboardingCompleto' => (bool) $tenant->onboarding_completado,
            'sector' => $tenant->sector,
            'idioma' => $tenant->idioma,
            'zonaHoraria' => $tenant->zona_horaria,
            'fiscal' => [
                'rfc' => $tenant->rfc_emisor,
                'razonSocial' => $tenant->razon_social_emisor,
                'regimenFiscal' => $tenant->regimen_fiscal_emisor,
                'codigoPostal' => $tenant->codigo_postal_emisor,
            ],
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
