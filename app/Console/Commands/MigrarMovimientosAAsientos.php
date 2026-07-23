<?php

namespace App\Console\Commands;

use App\Models\Erp\Asiento;
use App\Models\Erp\Movimiento;
use App\Services\Erp\AsientoService;
use App\Services\Erp\PlanCuentasService;
use Illuminate\Console\Command;

class MigrarMovimientosAAsientos extends Command
{
    protected $signature = 'contabilidad:migrar-movimientos';

    protected $description = 'Migra cada erp_movimientos existente (ingreso/egreso libre) a un asiento de 2 líneas usando cuentas puente (Caja vs Otros Ingresos/Gastos Generales). Idempotente.';

    public function handle(AsientoService $asientos, PlanCuentasService $cuentas): int
    {
        $movimientos = Movimiento::withoutGlobalScopes()->get();
        $migrados = 0;
        $omitidos = 0;

        foreach ($movimientos as $movimiento) {
            $yaExiste = Asiento::withoutGlobalScopes()
                ->where('referencia_tipo', Movimiento::class)
                ->where('referencia_id', $movimiento->id)
                ->exists();

            if ($yaExiste) {
                $omitidos++;

                continue;
            }

            $idTenant = $movimiento->id_tenant;
            $cuentas->sembrarParaTenant($idTenant); // idempotente — por si el tenant aún no tiene plan de cuentas

            $caja = $cuentas->cuenta($idTenant, PlanCuentasService::CAJA)->id;
            $puente = $movimiento->tipo === 'ingreso'
                ? $cuentas->cuenta($idTenant, PlanCuentasService::OTROS_INGRESOS)->id
                : $cuentas->cuenta($idTenant, PlanCuentasService::GASTOS_GENERALES)->id;

            $lineas = $movimiento->tipo === 'ingreso'
                ? [
                    ['id_cuenta' => $caja, 'debe' => $movimiento->monto, 'descripcion' => $movimiento->concepto],
                    ['id_cuenta' => $puente, 'haber' => $movimiento->monto, 'descripcion' => $movimiento->categoria],
                ]
                : [
                    ['id_cuenta' => $puente, 'debe' => $movimiento->monto, 'descripcion' => $movimiento->categoria],
                    ['id_cuenta' => $caja, 'haber' => $movimiento->monto, 'descripcion' => $movimiento->concepto],
                ];

            $asientos->registrar(
                idTenant: $idTenant,
                fecha: $movimiento->fecha,
                concepto: $movimiento->concepto,
                origen: 'migracion',
                lineas: $lineas,
                referenciaTipo: Movimiento::class,
                referenciaId: $movimiento->id,
            );

            $migrados++;
        }

        $this->info("Migrados: {$migrados}. Ya existentes (omitidos): {$omitidos}.");

        return self::SUCCESS;
    }
}
