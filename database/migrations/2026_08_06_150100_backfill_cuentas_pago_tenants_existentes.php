<?php

use App\Models\Erp\CuentaContable;
use App\Models\Tenant;
use App\Services\Erp\PlanCuentasService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * PlanCuentasService::sembrarParaTenant() solo siembra el plan de cuentas
     * una vez, al crear el tenant -- los tenants que ya existían antes de
     * agregar las cuentas de Bancos/Tarjeta por Cobrar no las tienen, y
     * AsientoService::cuenta() usa firstOrFail(), así que hay que
     * completarlas acá para no romper el registro de ventas con tarjeta.
     */
    public function up(): void
    {
        $nuevasCuentas = [
            ['codigo' => PlanCuentasService::BANCOS, 'nombre' => 'Bancos'],
            ['codigo' => PlanCuentasService::TARJETA_POR_COBRAR, 'nombre' => 'Tarjeta por Cobrar'],
        ];

        Tenant::all()->each(function (Tenant $tenant) use ($nuevasCuentas) {
            $grupoActivo = CuentaContable::withoutGlobalScopes()
                ->where('id_tenant', $tenant->id_tenant)
                ->where('codigo', '1000')
                ->first();

            // Tenant sin plan de cuentas sembrado en absoluto (no debería
            // pasar en producción, pero por si acaso): nada que backfillear,
            // sembrarParaTenant() se encargará del catálogo completo cuando
            // corresponda.
            if (! $grupoActivo) {
                return;
            }

            foreach ($nuevasCuentas as $cuenta) {
                CuentaContable::withoutGlobalScopes()->firstOrCreate(
                    ['id_tenant' => $tenant->id_tenant, 'codigo' => $cuenta['codigo']],
                    [
                        'nombre' => $cuenta['nombre'],
                        'tipo' => 'activo',
                        'naturaleza' => 'deudora',
                        'id_cuenta_padre' => $grupoActivo->id,
                        'es_movible' => true,
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        CuentaContable::withoutGlobalScopes()
            ->whereIn('codigo', [PlanCuentasService::BANCOS, PlanCuentasService::TARJETA_POR_COBRAR])
            ->delete();
    }
};
