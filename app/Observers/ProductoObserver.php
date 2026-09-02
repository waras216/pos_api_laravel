<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuarios;
use App\Notifications\StockBajoNotification;
use App\Services\IntegracionService;
use App\Services\Whatsapp\WhatsappDriverInterface;
use App\Services\Whatsapp\WhatsappException;
use Illuminate\Support\Facades\Log;

/**
 * Centraliza la alerta de stock bajo en un solo lugar en vez de repetir el
 * chequeo en cada controller que decrementa stock (Ventas, Mesas de
 * restaurante, Recetas de farmacia, Habitaciones, ajuste manual de
 * Inventario) — a todos les llega gratis con solo actualizar el modelo.
 */
class ProductoObserver
{
    public function __construct(private WhatsappDriverInterface $whatsapp) {}

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

        if (! $cruzoElUmbral) {
            return;
        }

        $idsAdmin = Rol::idsAdminTenant($producto->id_tenant);
        $admins = Usuarios::whereIn('id_usuario', $idsAdmin)->get();

        if (IntegracionService::conectada($producto->id_tenant, 'email')) {
            foreach ($admins->whereNotNull('email') as $admin) {
                $admin->notify(new StockBajoNotification($producto));
            }
        }

        if (IntegracionService::conectada($producto->id_tenant, 'whatsapp')) {
            $mensaje = "Alerta de inventario: \"{$producto->nombre}\""
                . ($producto->sku ? " (SKU {$producto->sku})" : '')
                . " llegó a stock bajo. Stock actual: {$producto->stock}, mínimo: {$producto->stock_minimo}.";

            foreach ($admins->whereNotNull('telefono') as $admin) {
                try {
                    $this->whatsapp->enviar($producto->id_tenant, $admin->telefono, $mensaje);
                } catch (WhatsappException $e) {
                    Log::warning("Alerta de stock bajo: envío de WhatsApp falló para producto #{$producto->id_productos}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
