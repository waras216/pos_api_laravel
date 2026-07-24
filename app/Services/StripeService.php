<?php

namespace App\Services;

use App\Models\Tenant;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\Webhook;

/**
 * Wrapper delgado sobre el SDK de Stripe. No conoce el modelo de negocio
 * (Suscripcion/Plan) -- eso vive en SuscripcionService -- para poder
 * mockear las llamadas de red en tests sin tocar la lógica de negocio.
 */
class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.key'));
    }

    public function crearCustomer(Tenant $tenant, string $email): string
    {
        $customer = $this->client->customers->create([
            'name' => $tenant->nombre_tenant,
            'email' => $email,
            'metadata' => ['id_tenant' => $tenant->id_tenant],
        ]);

        return $customer->id;
    }

    public function crearCheckoutSession(
        string $customerId,
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        array $metadata = []
    ): CheckoutSession {
        return $this->client->checkout->sessions->create([
            'mode' => 'subscription',
            'customer' => $customerId,
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'subscription_data' => [
                'metadata' => $metadata,
            ],
            'metadata' => $metadata,
        ]);
    }

    public function crearPortalSession(string $customerId, string $returnUrl): PortalSession
    {
        return $this->client->billingPortal->sessions->create([
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);
    }

    public function obtenerSuscripcion(string $subscriptionId): Subscription
    {
        return $this->client->subscriptions->retrieve($subscriptionId, [
            'expand' => ['items.data.price'],
        ]);
    }

    public function construirEventoWebhook(string $payload, string $firma): Event
    {
        return Webhook::constructEvent(
            $payload,
            $firma,
            config('services.stripe.webhook_secret')
        );
    }
}
