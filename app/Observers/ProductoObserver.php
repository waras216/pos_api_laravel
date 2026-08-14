<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuarios;
use App\Notifications\StockBajoNotification;
use App\Services\IntegracionService;

/**
 * Centraliza la alerta de stock bajo en un solo lugar en vez de repetir el
 * chequeo en cada controller que decrementa stock (Ventas, Mesas de
 * restaurante, Recetas de farmacia, Habitaciones, ajuste manual de
 * Inventario) — a todos les llega gratis con solo actualizar el modelo.
 */
class ProductoObserver
{
    public function updated(Producto $producto): void
    {
        if (! $producto->wasChanged('stock')) {
            return;
        }

        $stockAnterior = (int) $producto->getOriginal('stock');
        $stockActual = (int) $producto->stock;

        // Solo notifica al *cruzar* el umbral hacia abajo, no en cada venta
        // subsiguiente mientras ya está bajo — evita spam.
        $cruzoElUmbral = $stockActual <= $producto->stock_minimo && $stockAnterior > $producto->stock_minimo;

        if (! $cruzoElUmbral || ! IntegracionService::conectada($producto->id_tenant, 'email')) {
            return;
        }

        $idsAdmin = Rol::idsAdminTenant($producto->id_tenant);
        $admins = Usuarios::whereIn('id_usuario', $idsAdmin)->whereNotNull('email')->get();

        foreach ($admins as $admin) {
            $admin->notify(new StockBajoNotification($producto));
        }
    }
}
