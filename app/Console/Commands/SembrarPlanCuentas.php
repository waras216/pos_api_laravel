<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Erp\PlanCuentasService;
use Illuminate\Console\Command;

class SembrarPlanCuentas extends Command
{
    protected $signature = 'contabilidad:sembrar-plan-cuentas {tenant? : id_tenant específico, o todos si se omite}';

    protected $description = 'Crea el plan de cuentas por defecto para un tenant (o para todos los que aún no tengan uno)';

    public function handle(PlanCuentasService $servicio): int
    {
        $tenants = $this->argument('tenant')
            ? Tenant::where('id_tenant', $this->argument('tenant'))->get()
            : Tenant::all();

        foreach ($tenants as $tenant) {
            $servicio->sembrarParaTenant($tenant->id_tenant);
            $this->info("Tenant {$tenant->id_tenant} ({$tenant->nombre_tenant}): plan de cuentas OK");
        }

        return self::SUCCESS;
    }
}
