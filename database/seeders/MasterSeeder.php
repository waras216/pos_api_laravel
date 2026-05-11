<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // Negocios
        DB::table('negocios')->insert([
            ['nombre_negocio' => 'Restaurante', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_negocio' => 'Tienda',      'created_at' => now(), 'updated_at' => now()],
            ['nombre_negocio' => 'Cafetería',   'created_at' => now(), 'updated_at' => now()],
        ]);

        // Plans
        DB::table('plans')->insert([
            [
                'nombre_plan'  => 'Básico',
                'precio'       => 299.00,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin'    => '2026-12-31',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre_plan'  => 'Pro',
                'precio'       => 599.00,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin'    => '2026-12-31',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre_plan'  => 'Enterprise',
                'precio'       => 999.00,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin'    => '2026-12-31',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        // Tenant
        DB::table('tenants')->insert([
            [
                'nombre_tenant'  => 'Demo Company',
                'subdominio'     => 'demo',
                'estado'         => 'activo',
                'id_tiponegocio' => 1,
                'id_plan'        => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        DB::table('usuarios')->insert([
            [
            'id_tenant'  => 1,
            'nombre'     => 'Admin Demo',
            'email'      => 'admin@demo.com',
            'password'   => Hash::make('123456'),
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);

        // Categorias
        DB::table('categorias')->insert([
            ['id_tenant' => 1, 'nombre' => 'Alimentos',  'descripcion' => 'Comida y alimentos', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'nombre' => 'Bebidas',    'descripcion' => 'Bebidas frías y calientes', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'nombre' => 'Snacks',     'descripcion' => 'Botanas y snacks', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Productos
        DB::table('productos')->insert([
            ['id_tenant' => 1, 'id_categorias' => 1, 'nombre' => 'Torta de jamón',  'precio' => 55.00, 'stock' => 50, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 1, 'nombre' => 'Quesadilla',      'precio' => 45.00, 'stock' => 30, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 2, 'nombre' => 'Agua 600ml',      'precio' => 18.00, 'stock' => 100, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 2, 'nombre' => 'Refresco 355ml',  'precio' => 22.00, 'stock' => 80, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 2, 'nombre' => 'Café americano',  'precio' => 35.00, 'stock' => 60, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 3, 'nombre' => 'Papas Sabritas',  'precio' => 16.00, 'stock' => 40, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_tenant' => 1, 'id_categorias' => 3, 'nombre' => 'Chocolatín',      'precio' => 20.00, 'stock' => 35, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}