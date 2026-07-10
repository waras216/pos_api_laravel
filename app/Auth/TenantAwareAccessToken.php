<?php

namespace App\Auth;

use App\Models\Rol;
use App\Models\Usuarios;
use DateTimeImmutable;
use Laravel\Passport\Bridge\AccessToken;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

/**
 * Igual que Laravel\Passport\Bridge\AccessToken, pero agrega tenant_id, roles
 * y perm_ver al JWT (ver §06 del documento de arquitectura). convertToJWT()
 * en el trait de League es privado y no se puede extender, así que acá se
 * reconstruye el token completo en toString().
 */
class TenantAwareAccessToken extends AccessToken
{
    use AccessTokenTrait;

    public function toString(): string
    {
        $this->initJwtConfiguration();

        $builder = $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($this->getUserIdentifier() ?? $this->getClient()->getIdentifier())
            ->withClaim('scopes', $this->getScopes());

        foreach ($this->claimsDeTenant() as $clave => $valor) {
            $builder = $builder->withClaim($clave, $valor);
        }

        return $builder->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey())->toString();
    }

    private function claimsDeTenant(): array
    {
        $idUsuario = $this->getUserIdentifier();
        if ($idUsuario === null) {
            return [];
        }

        $usuario = Usuarios::find($idUsuario);
        if (! $usuario) {
            return [];
        }

        return [
            'tenant_id' => $usuario->id_tenant,
            'roles' => $this->rolesDe($usuario),
            'perm_ver' => 1,
        ];
    }

    private function rolesDe(Usuarios $usuario): array
    {
        $roles = [];

        if (Rol::esAdminTenant($usuario->id_usuario, $usuario->id_tenant)) {
            $roles[] = 'tenant.admin';
        }

        if (Rol::esSuperAdmin($usuario->id_usuario)) {
            $roles[] = 'platform.superadmin';
        }

        return $roles;
    }
}
