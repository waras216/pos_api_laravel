<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Las tablas rols/permisos/permiso_rol quedaron huérfanas de un intento anterior:
        // vacías, sin FK desde usuarios, sin ningún controller o seeder que las use.
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('rols');
        Schema::dropIfExists('permisos');

        Schema::create('modulos', function (Blueprint $table) {
            $table->id('id_modulo');
            $table->string('clave', 40)->unique();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->foreignId('id_tenant')->nullable()
                ->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_modulo')->nullable()
                ->constrained('modulos', 'id_modulo')->cascadeOnDelete();
            $table->string('clave', 80);
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('es_sistema')->default(false);
            $table->timestamps();

            $table->unique(['id_tenant', 'clave']);
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id('id_permiso');
            $table->string('clave', 120)->unique();
            $table->foreignId('id_modulo')->nullable()
                ->constrained('modulos', 'id_modulo')->cascadeOnDelete();
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });

        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->foreignId('id_rol')->constrained('roles', 'id_rol')->cascadeOnDelete();
            $table->foreignId('id_permiso')->constrained('permisos', 'id_permiso')->cascadeOnDelete();
            $table->primary(['id_rol', 'id_permiso']);
        });

        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->id('id_usuario_rol');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->foreignId('id_rol')->constrained('roles', 'id_rol')->cascadeOnDelete();
            $table->foreignId('asignado_por')->nullable()
                ->constrained('usuarios', 'id_usuario')->nullOnDelete();
            $table->timestamp('asignado_en')->nullable();
            $table->timestamps();

            $table->unique(['id_usuario', 'id_tenant', 'id_rol']);
        });

        Schema::create('usuario_rol_global', function (Blueprint $table) {
            $table->id('id_usuario_rol_global');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
            $table->foreignId('id_rol')->constrained('roles', 'id_rol')->cascadeOnDelete();
            $table->foreignId('asignado_por')->nullable()
                ->constrained('usuarios', 'id_usuario')->nullOnDelete();
            $table->timestamp('asignado_en')->nullable();
            $table->timestamps();

            $table->unique(['id_usuario', 'id_rol']);
        });

        $this->sembrarModulos();
        $this->migrarBooleanosARoles();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_rol_global');
        Schema::dropIfExists('usuario_rol');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('modulos');
    }

    private function sembrarModulos(): void
    {
        foreach (['crm' => 'CRM', 'erp' => 'ERP', 'pos' => 'POS'] as $clave => $nombre) {
            DB::table('modulos')->updateOrInsert(
                ['clave' => $clave],
                ['nombre' => $nombre, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Backfill: es_admin=true -> fila en usuario_rol con el rol de sistema
     * "tenant.admin" de su tenant; es_superadmin=true -> usuario_rol_global
     * con el rol de sistema "platform.superadmin". Deja usuarios.es_admin/
     * es_superadmin como columnas vestigiales (ya no se leen desde el código).
     */
    private function migrarBooleanosARoles(): void
    {
        $idRolSuperadmin = DB::table('roles')->insertGetId([
            'id_tenant' => null,
            'id_modulo' => null,
            'clave' => 'platform.superadmin',
            'nombre' => 'Super Administrador',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_rol');

        $tenantIds = DB::table('usuarios')->select('id_tenant')->distinct()->pluck('id_tenant');

        foreach ($tenantIds as $idTenant) {
            $idRolAdmin = DB::table('roles')->insertGetId([
                'id_tenant' => $idTenant,
                'id_modulo' => null,
                'clave' => 'tenant.admin',
                'nombre' => 'Administrador',
                'es_sistema' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_rol');

            $usuariosAdmin = DB::table('usuarios')
                ->where('id_tenant', $idTenant)
                ->where('es_admin', true)
                ->pluck('id_usuario');

            foreach ($usuariosAdmin as $idUsuario) {
                DB::table('usuario_rol')->insert([
                    'id_usuario' => $idUsuario,
                    'id_tenant' => $idTenant,
                    'id_rol' => $idRolAdmin,
                    'asignado_en' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $usuariosSuperadmin = DB::table('usuarios')->where('es_superadmin', true)->pluck('id_usuario');

        foreach ($usuariosSuperadmin as $idUsuario) {
            DB::table('usuario_rol_global')->insert([
                'id_usuario' => $idUsuario,
                'id_rol' => $idRolSuperadmin,
                'asignado_en' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
