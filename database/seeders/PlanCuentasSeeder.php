<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Services\Erp\PlanCuentasService;
use Illuminate\Database\Seeder;

class PlanCuentasSeeder extends Seeder
{
    public function run(): void
    {
        $servicio = app(PlanCuentasService::class);

        Tenant::all()->each(fn (Tenant $tenant) => $servicio->sembrarParaTenant($tenant->id_tenant));
    }
}
