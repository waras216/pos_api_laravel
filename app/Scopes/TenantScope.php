<?php

namespace App\Scopes;

use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra automáticamente por el id_tenant activo del usuario autenticado.
 * Solo aplica cuando hay un usuario resuelto por el guard (auth:sanctum ya
 * corrió Auth::shouldUse('sanctum') para la request) -- en consola/tinker
 * sin usuario autenticado, no filtra nada.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user instanceof Usuarios && $user->id_tenant) {
            $builder->where($model->qualifyColumn('id_tenant'), $user->id_tenant);
        }
    }
}
