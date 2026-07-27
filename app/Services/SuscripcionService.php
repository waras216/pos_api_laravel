<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Suscripcion;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Subscription as StripeSubscription;

class SuscripcionService
{
    // Mapeo del status que manda Stripe a los estados internos de Suscripcion.
    private const MAPA_ESTADOS = [
        'active' => 'activa',
        'trialing' => 'activa',
        'past_due' => 'periodo_gracia',
        'unpaid' => 'vencida',
        'incomplete_expired' => 'vencida',
        'canceled' => 'cancelada',
        'incomplete' => 'incompleta',
    ];

    public function __construct(private StripeService $stripe) {}

    public function obtenerOCrearCustomer(Tenant $tenant, string $email): string
    {
        if ($tenant->stripe_customer_id) {
            return $tenant->stripe_customer_id;
        }

        $customerId = $this->stripe->crearCustomer($tenant, $email);
        $tenant->update(['stripe_customer_id' => $customerId]);

        return $customerId;
    }

    public function iniciarCheckout(Tenant $tenant, Plan $plan, string $email): string
    {
        $customerId = $this->obtenerOCrearCustomer($tenant, $email);

        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        $session = $this->stripe->crearCheckoutSession(
            customerId: $customerId,
            priceId: $plan->stripe_price_id,
            successUrl: $frontendUrl . '/configuracion/suscripcion?checkout=success',
            cancelUrl: $frontendUrl . '/configuracion/suscripcion?checkout=cancel',
            metadata: [
                'id_tenant' => (string) $tenant->id_tenant,
                'id_plan' => (string) $plan->id_plan,
            ],
        );

        return $session->url;
    }

    public function abrirPortal(Tenant $tenant): string
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        $session = $this->stripe->crearPortalSession(
            customerId: $tenant->stripe_customer_id,
            returnUrl: $frontendUrl . '/configuracion/suscripcion',
        );

        return $session->url;
    }

    /**
     * Único punto de entrada del webhook. Idempotente: si ya procesamos
     * este stripe_event_id, no vuelve a aplicar el evento.
     */
    public function procesarEvento(Event $event): void
    {
        $registro = StripeWebhookEvent::firstOrCreate(
            ['stripe_event_id' => $event->id],
            ['tipo' => $event->type],
        );

        if ($registro->procesado_en !== null) {
            return;
        }

        match ($event->type) {
            'checkout.session.completed' => $this->manejarCheckoutCompletado($event),
            'customer.subscription.updated' => $this->manejarSuscripcionActualizada($event),
            'customer.subscription.deleted' => $this->manejarSuscripcionEliminada($event),
            'invoice.payment_failed' => $this->manejarPagoFallido($event),
            default => null,
        };

        $registro->update(['procesado_en' => now()]);
    }

    private function manejarCheckoutCompletado(Event $event): void
    {
        $session = $event->data->object;

        if (! $session->subscription) {
            return;
        }

        $subscription = $this->stripe->obtenerSuscripcion($session->subscription);
        $idTenant = $session->metadata->id_tenant ?? null;

        $this->sincronizarDesdeStripeSubscription($subscription, $idTenant ? (int) $idTenant : null);
    }

    private function manejarSuscripcionActualizada(Event $event): void
    {
        $subscription = $event->data->object;
        $idTenant = $subscription->metadata->id_tenant ?? null;

        $this->sincronizarDesdeStripeSubscription($subscription, $idTenant ? (int) $idTenant : null);
    }

    private function manejarSuscripcionEliminada(Event $event): void
    {
        $subscription = $event->data->object;
        $idTenant = $subscription->metadata->id_tenant ?? null;

        $this->sincronizarDesdeStripeSubscription($subscription, $idTenant ? (int) $idTenant : null);
    }

    private function manejarPagoFallido(Event $event): void
    {
        $invoice = $event->data->object;

        if (! $invoice->subscription) {
            return;
        }

        Suscripcion::where('stripe_subscription_id', $invoice->subscription)
            ->update(['estado' => 'periodo_gracia', 'ultimo_evento_stripe' => $event->toArray()]);
    }

    public function sincronizarDesdeStripeSubscription(StripeSubscription $subscription, ?int $idTenant = null): Suscripcion
    {
        $existente = Suscripcion::where('stripe_subscription_id', $subscription->id)->first();
        $idTenant ??= $existente?->id_tenant;

        if (! $idTenant) {
            Log::warning('Webhook de Stripe sin id_tenant resoluble', ['stripe_subscription_id' => $subscription->id]);
            throw new \RuntimeException("No se pudo resolver id_tenant para la suscripción {$subscription->id}");
        }

        $item = $subscription->items->data[0] ?? null;
        $priceId = $item?->price->id ?? null;
        $plan = $priceId ? Plan::where('stripe_price_id', $priceId)->first() : null;
        $currentPeriodEnd = $item?->current_period_end ?? $subscription->current_period_end ?? null;

        $suscripcion = Suscripcion::updateOrCreate(
            ['stripe_subscription_id' => $subscription->id],
            [
                'id_tenant' => $idTenant,
                'id_plan' => $plan?->id_plan ?? $existente?->id_plan,
                'stripe_price_id' => $priceId,
                'estado' => self::MAPA_ESTADOS[$subscription->status] ?? 'incompleta',
                'fecha_inicio' => $subscription->start_date ? now()->createFromTimestamp($subscription->start_date) : null,
                'fecha_fin_periodo_actual' => $currentPeriodEnd ? now()->createFromTimestamp($currentPeriodEnd) : null,
                'cancela_al_final_periodo' => (bool) $subscription->cancel_at_period_end,
                'fecha_cancelacion' => $subscription->canceled_at ? now()->createFromTimestamp($subscription->canceled_at) : null,
                'ultimo_evento_stripe' => $subscription->toArray(),
            ],
        );

        if ($plan && in_array($suscripcion->estado, ['activa', 'periodo_gracia'], true)) {
            Tenant::where('id_tenant', $idTenant)->update(['id_plan' => $plan->id_plan]);
        }

        return $suscripcion;
    }
}
