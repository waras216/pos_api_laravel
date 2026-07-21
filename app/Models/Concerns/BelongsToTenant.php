<?php

namespace App\Models\Concerns;

use App\Models\Usuarios;
use App\Scopes\TenantScope;

/**
 * Aplica el filtrado automático por id_tenant (TenantScope) a este modelo y
 * rellena id_tenant al crear si no vino seteado. Esto complementa -- no
 * reemplaza -- el filtrado manual explícito que ya hacen los controladores
 * (ver CLAUDE.md); es una red de seguridad para no depender únicamente de
 * que cada controlador recuerde filtrar.
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (empty($model->id_tenant)) {
                $user = auth()->user();
                if ($user instanceof Usuarios && $user->id_tenant) {
                    $model->id_tenant = $user->id_tenant;
                }
            }
        });
    }
}
