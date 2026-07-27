<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Services\SuscripcionService;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function __construct(private SuscripcionService $suscripciones) {}

    public function show(Request $request)
    {
        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();

        return response()->json($tenant->suscripcionActual);
    }

    public function planes()
    {
        return response()->json(Plan::orderBy('id_plan')->get());
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'id_plan' => 'required|integer|exists:plans,id_plan',
        ]);

        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();
        $plan = Plan::findOrFail($data['id_plan']);

        if (! $plan->stripe_price_id) {
            return response()->json(['message' => 'Este plan no tiene un precio de Stripe configurado'], 422);
        }

        $url = $this->suscripciones->iniciarCheckout($tenant, $plan, $request->user()->email);

        return response()->json(['url' => $url]);
    }

    public function portal(Request $request)
    {
        $tenant = Tenant::where('id_tenant', $request->user()->id_tenant)->firstOrFail();

        if (! $tenant->stripe_customer_id) {
            return response()->json(['message' => 'Este tenant todavía no tiene una suscripción con Stripe'], 422);
        }

        $url = $this->suscripciones->abrirPortal($tenant);

        return response()->json(['url' => $url]);
    }
}
