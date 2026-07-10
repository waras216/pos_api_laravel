<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client;

/**
 * Todos los clientes OAuth de STRATo Hub son de primera parte (la propia
 * SPA); no existe todavía un Marketplace de integraciones de terceros que
 * justifique mostrar una pantalla de consentimiento. Cuando eso exista, acá
 * es donde se distingue un cliente de confianza de uno externo.
 */
class StratoPassportClient extends Client
{
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return true;
    }
}
