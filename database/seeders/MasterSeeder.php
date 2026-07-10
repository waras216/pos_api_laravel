<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // Negocios (los 6 nichos reales del onboarding, mapeados por slug)
        foreach ([
            ['slug' => 'hotel',       'nombre_negocio' => 'Hotel'],
            ['slug' => 'restaurante', 'nombre_negocio' => 'Restaurante'],
            ['slug' => 'almacen',     'nombre_negocio' => 'Almacén / Bodega'],
            ['slug' => 'farmacia',    'nombre_negocio' => 'Farmacia'],
            ['slug' => 'startup',     'nombre_negocio' => 'Startup'],
            ['slug' => 'tienda',      'nombre_negocio' => 'Tienda / Retail'],
        ] as $negocio) {
            DB::table('negocios')->updateOrInsert(
                ['slug' => $negocio['slug']],
                ['nombre_negocio' => $negocio['nombre_negocio'], 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // Plans
        DB::table('plans')->insert([
            [
                'nombre_plan'  => 'Básico',
                'precio'       => 299.00,
                'max_usuarios' => 3,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin'    => '2026-12-31',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre_plan'  => 'Pro',
                'precio'       => 599.00,
                'max_usuarios' => 15,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin'    => '2026-12-31',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre_plan'  => 'Enterprise',
                'precio'       => 999.00,
                'max_usuarios' => null,
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
                'id_tiponegocio' => null,
                'id_plan'        => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        $idUsuarioDemo = DB::table('usuarios')->insertGetId([
            'id_tenant'      => 1,
            'nombre'         => 'Admin Demo',
            'email'          => 'admin@demo.com',
            'password'       => Hash::make('123456'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ], 'id_usuario');

        // Módulos y roles de sistema
        foreach (['crm' => 'CRM', 'erp' => 'ERP', 'pos' => 'POS'] as $clave => $nombre) {
            DB::table('modulos')->updateOrInsert(
                ['clave' => $clave],
                ['nombre' => $nombre, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        DB::table('roles')->updateOrInsert(
            ['id_tenant' => null, 'clave' => 'platform.superadmin'],
            ['nombre' => 'Super Administrador', 'es_sistema' => true, 'created_at' => now(), 'updated_at' => now()]
        );
        $idRolSuperadmin = DB::table('roles')->whereNull('id_tenant')->where('clave', 'platform.superadmin')->value('id_rol');

        DB::table('usuario_rol_global')->insert([
            'id_usuario'  => $idUsuarioDemo,
            'id_rol'      => $idRolSuperadmin,
            'asignado_en' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        Rol::asignarTenantAdmin($idUsuarioDemo, 1);

        DB::table('membresias')->insert([
            'id_usuario' => $idUsuarioDemo,
            'id_tenant'  => 1,
            'estado'     => 'activa',
            'es_owner'   => true,
            'unido_en'   => now(),
            'created_at' => now(),
            'updated_at' => now(),
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
