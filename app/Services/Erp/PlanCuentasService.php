<?php

namespace App\Services\Erp;

use App\Models\Erp\CuentaContable;

class PlanCuentasService
{
    // Códigos de las cuentas movibles que consume AsientoService al generar
    // asientos automáticos — mantener sincronizados con el catálogo de abajo.
    public const CAJA = '1100';

    public const CUENTAS_POR_COBRAR = '1200';

    public const INVENTARIO = '1300';

    public const CUENTAS_POR_PAGAR = '2100';

    public const NOMINA_POR_PAGAR = '2200';

    public const INGRESOS_VENTAS = '4100';

    public const OTROS_INGRESOS = '4900';

    public const COSTO_MERCANCIA_VENDIDA = '5100';

    public const GASTOS_NOMINA = '5200';

    public const GASTOS_GENERALES = '5900';

    private const GRUPOS = [
        ['codigo' => '1000', 'nombre' => 'Activo', 'tipo' => 'activo', 'naturaleza' => 'deudora'],
        ['codigo' => '2000', 'nombre' => 'Pasivo', 'tipo' => 'pasivo', 'naturaleza' => 'acreedora'],
        ['codigo' => '3000', 'nombre' => 'Capital', 'tipo' => 'capital', 'naturaleza' => 'acreedora'],
        ['codigo' => '4000', 'nombre' => 'Ingresos', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora'],
        ['codigo' => '5000', 'nombre' => 'Costos y Gastos', 'tipo' => 'gasto', 'naturaleza' => 'deudora'],
    ];

    private const CUENTAS = [
        ['codigo' => self::CAJA, 'nombre' => 'Caja y Bancos', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'padre' => '1000'],
        ['codigo' => self::CUENTAS_POR_COBRAR, 'nombre' => 'Cuentas por Cobrar Clientes', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'padre' => '1000'],
        ['codigo' => self::INVENTARIO, 'nombre' => 'Inventario', 'tipo' => 'activo', 'naturaleza' => 'deudora', 'padre' => '1000'],
        ['codigo' => self::CUENTAS_POR_PAGAR, 'nombre' => 'Cuentas por Pagar Proveedores', 'tipo' => 'pasivo', 'naturaleza' => 'acreedora', 'padre' => '2000'],
        ['codigo' => self::NOMINA_POR_PAGAR, 'nombre' => 'Nómina por Pagar', 'tipo' => 'pasivo', 'naturaleza' => 'acreedora', 'padre' => '2000'],
        ['codigo' => '3100', 'nombre' => 'Capital Social', 'tipo' => 'capital', 'naturaleza' => 'acreedora', 'padre' => '3000'],
        ['codigo' => '3200', 'nombre' => 'Resultados Acumulados', 'tipo' => 'capital', 'naturaleza' => 'acreedora', 'padre' => '3000'],
        ['codigo' => self::INGRESOS_VENTAS, 'nombre' => 'Ingresos por Ventas', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'padre' => '4000'],
        ['codigo' => self::OTROS_INGRESOS, 'nombre' => 'Otros Ingresos', 'tipo' => 'ingreso', 'naturaleza' => 'acreedora', 'padre' => '4000'],
        ['codigo' => self::COSTO_MERCANCIA_VENDIDA, 'nombre' => 'Costo de Mercancía Vendida', 'tipo' => 'costo', 'naturaleza' => 'deudora', 'padre' => '5000'],
        ['codigo' => self::GASTOS_NOMINA, 'nombre' => 'Gastos de Nómina', 'tipo' => 'gasto', 'naturaleza' => 'deudora', 'padre' => '5000'],
        ['codigo' => self::GASTOS_GENERALES, 'nombre' => 'Gastos Generales', 'tipo' => 'gasto', 'naturaleza' => 'deudora', 'padre' => '5000'],
    ];

    /**
     * Crea el plan de cuentas por defecto para un tenant. Idempotente: no
     * hace nada si el tenant ya tiene alguna cuenta.
     */
    public function sembrarParaTenant(int $idTenant): void
    {
        if (CuentaContable::withoutGlobalScopes()->where('id_tenant', $idTenant)->exists()) {
            return;
        }

        $idsPorCodigo = [];

        foreach (self::GRUPOS as $grupo) {
            $cuenta = CuentaContable::create([
                'id_tenant' => $idTenant,
                'codigo' => $grupo['codigo'],
                'nombre' => $grupo['nombre'],
                'tipo' => $grupo['tipo'],
                'naturaleza' => $grupo['naturaleza'],
                'es_movible' => false,
            ]);
            $idsPorCodigo[$grupo['codigo']] = $cuenta->id;
        }

        foreach (self::CUENTAS as $cuenta) {
            CuentaContable::create([
                'id_tenant' => $idTenant,
                'codigo' => $cuenta['codigo'],
                'nombre' => $cuenta['nombre'],
                'tipo' => $cuenta['tipo'],
                'naturaleza' => $cuenta['naturaleza'],
                'id_cuenta_padre' => $idsPorCodigo[$cuenta['padre']],
                'es_movible' => true,
            ]);
        }
    }

    public function cuenta(int $idTenant, string $codigo): CuentaContable
    {
        return CuentaContable::withoutGlobalScopes()
            ->where('id_tenant', $idTenant)
            ->where('codigo', $codigo)
            ->firstOrFail();
    }
}
