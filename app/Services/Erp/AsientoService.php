<?php

namespace App\Services\Erp;

use App\Models\Erp\Asiento;
use App\Models\Erp\CuentaContable;
use App\Models\Erp\OrdenCompra;
use App\Models\Erp\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsientoService
{
    public function __construct(private PlanCuentasService $cuentas) {}

    /**
     * Registra un asiento validando que las líneas balanceen (Σdebe = Σhaber)
     * y que cada línea tenga debe XOR haber > 0. Encabezado + líneas se
     * persisten en una sola transacción.
     *
     * @param  array<int, array{id_cuenta:int, debe?:float, haber?:float, descripcion?:string}>  $lineas
     */
    public function registrar(
        int $idTenant,
        string $fecha,
        string $concepto,
        string $origen,
        array $lineas,
        ?string $referenciaTipo = null,
        ?int $referenciaId = null,
        ?int $idUsuario = null,
    ): Asiento {
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($lineas as $linea) {
            $debe = round((float) ($linea['debe'] ?? 0), 2);
            $haber = round((float) ($linea['haber'] ?? 0), 2);

            if (($debe > 0) === ($haber > 0)) {
                throw ValidationException::withMessages([
                    'lineas' => 'Cada línea del asiento debe tener un monto en debe o en haber, no ambos ni ninguno.',
                ]);
            }

            $totalDebe += $debe;
            $totalHaber += $haber;
        }

        if (round($totalDebe, 2) !== round($totalHaber, 2)) {
            throw ValidationException::withMessages([
                'lineas' => 'El asiento no balancea: la suma del debe debe ser igual a la suma del haber.',
            ]);
        }

        return DB::transaction(function () use ($idTenant, $fecha, $concepto, $origen, $lineas, $referenciaTipo, $referenciaId, $idUsuario, $totalDebe, $totalHaber) {
            $asiento = Asiento::create([
                'id_tenant' => $idTenant,
                'fecha' => $fecha,
                'concepto' => $concepto,
                'origen' => $origen,
                'referencia_tipo' => $referenciaTipo,
                'referencia_id' => $referenciaId,
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'id_usuario' => $idUsuario,
            ]);

            foreach ($lineas as $linea) {
                $asiento->detalles()->create([
                    'id_cuenta' => $linea['id_cuenta'],
                    'debe' => $linea['debe'] ?? 0,
                    'haber' => $linea['haber'] ?? 0,
                    'descripcion' => $linea['descripcion'] ?? null,
                ]);
            }

            return $asiento->load('detalles.cuenta');
        });
    }

    /**
     * Reconoce el ingreso y el costo de una venta facturada:
     * Debe Caja/Bancos/Tarjeta por Cobrar (una línea por cada pago del
     * pedido, según su método) + Costo de Mercancía Vendida, Haber Ingresos
     * por Ventas + Inventario. No genera nada si ya existe un asiento con
     * esta referencia (evita duplicar si el mismo pedido se procesa más de
     * una vez por error).
     */
    public function registrarVenta(Pedido $pedido): ?Asiento
    {
        if ($this->existeAsientoPara(Pedido::class, $pedido->id)) {
            return null;
        }

        $idTenant = $pedido->id_tenant;
        $costoTotal = round(
            $pedido->items->sum(fn ($item) => (float) $item->cantidad * (float) ($item->costo_unitario ?? 0)),
            2
        );

        $lineas = [];

        foreach ($pedido->pagos as $pago) {
            $lineas[] = [
                'id_cuenta' => $this->cuentas->cuenta($idTenant, $this->cuentaParaMetodoPago($pago->metodo_pago))->id,
                'debe' => $pago->monto,
                'descripcion' => "Cobro pedido #{$pedido->id} ({$pago->metodo_pago})",
            ];
        }

        $lineas[] = ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::INGRESOS_VENTAS)->id, 'haber' => $pedido->total, 'descripcion' => "Venta pedido #{$pedido->id}"];

        if ($costoTotal > 0) {
            $lineas[] = ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::COSTO_MERCANCIA_VENDIDA)->id, 'debe' => $costoTotal, 'descripcion' => "Costo de venta pedido #{$pedido->id}"];
            $lineas[] = ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::INVENTARIO)->id, 'haber' => $costoTotal, 'descripcion' => "Salida de inventario pedido #{$pedido->id}"];
        }

        return $this->registrar(
            idTenant: $idTenant,
            fecha: $pedido->fecha,
            concepto: "Venta facturada — Pedido #{$pedido->id}",
            origen: 'venta',
            lineas: $lineas,
            referenciaTipo: Pedido::class,
            referenciaId: $pedido->id,
        );
    }

    /**
     * Registra la recepción de una orden de compra a crédito:
     * Debe Inventario, Haber Cuentas por Pagar Proveedores.
     */
    public function registrarCompraRecibida(OrdenCompra $orden): ?Asiento
    {
        if ($this->existeAsientoPara(OrdenCompra::class, $orden->id)) {
            return null;
        }

        $idTenant = $orden->id_tenant;

        return $this->registrar(
            idTenant: $idTenant,
            fecha: $orden->fecha,
            concepto: "Compra recibida — Orden #{$orden->id}",
            origen: 'compra',
            lineas: [
                ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::INVENTARIO)->id, 'debe' => $orden->total, 'descripcion' => "Recepción orden #{$orden->id}"],
                ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::CUENTAS_POR_PAGAR)->id, 'haber' => $orden->total, 'descripcion' => "Orden #{$orden->id} a crédito"],
            ],
            referenciaTipo: OrdenCompra::class,
            referenciaId: $orden->id,
        );
    }

    /**
     * Registra el pago de nómina: Debe Gastos de Nómina, Haber Caja.
     */
    public function registrarNomina(int $idTenant, string $fecha, float $total, ?string $referenciaTipo = null, ?int $referenciaId = null): Asiento
    {
        return $this->registrar(
            idTenant: $idTenant,
            fecha: $fecha,
            concepto: 'Pago de nómina',
            origen: 'nomina',
            lineas: [
                ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::GASTOS_NOMINA)->id, 'debe' => $total, 'descripcion' => 'Gasto de nómina'],
                ['id_cuenta' => $this->cuentas->cuenta($idTenant, PlanCuentasService::CAJA)->id, 'haber' => $total, 'descripcion' => 'Pago de nómina'],
            ],
            referenciaTipo: $referenciaTipo,
            referenciaId: $referenciaId,
        );
    }

    /**
     * Revierte un asiento existente creando uno espejo (debe/haber invertidos).
     */
    public function reversar(Asiento $asiento, ?int $idUsuario = null): Asiento
    {
        $lineas = $asiento->detalles->map(fn ($d) => [
            'id_cuenta' => $d->id_cuenta,
            'debe' => $d->haber,
            'haber' => $d->debe,
            'descripcion' => 'Reversión: ' . ($d->descripcion ?? ''),
        ])->all();

        return $this->registrar(
            idTenant: $asiento->id_tenant,
            fecha: now()->toDateString(),
            concepto: "Reversión de asiento #{$asiento->id} — {$asiento->concepto}",
            origen: 'ajuste',
            lineas: $lineas,
            referenciaTipo: Asiento::class,
            referenciaId: $asiento->id,
            idUsuario: $idUsuario,
        );
    }

    private function cuentaParaMetodoPago(string $metodoPago): string
    {
        return match ($metodoPago) {
            'efectivo' => PlanCuentasService::CAJA,
            'tarjeta_debito' => PlanCuentasService::BANCOS,
            'tarjeta_credito' => PlanCuentasService::TARJETA_POR_COBRAR,
        };
    }

    private function existeAsientoPara(string $referenciaTipo, int $referenciaId): bool
    {
        return Asiento::withoutGlobalScopes()
            ->where('referencia_tipo', $referenciaTipo)
            ->where('referencia_id', $referenciaId)
            ->exists();
    }
}
