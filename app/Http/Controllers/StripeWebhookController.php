<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\SuscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private StripeService $stripe,
        private SuscripcionService $suscripciones,
    ) {}

    public function handle(Request $request)
    {
        try {
            $event = $this->stripe->construirEventoWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
            );
        } catch (SignatureVerificationException|UnexpectedValueException $e) {
            Log::warning('Firma de webhook de Stripe inválida', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Firma inválida'], 400);
        }

        $this->suscripciones->procesarEvento($event);

        return response()->json(['recibido' => true]);
    }
}
