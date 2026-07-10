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
        Schema::create('membresias', function (Blueprint $table) {
            $table->id('id_membresia');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->cascadeOnDelete();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->enum('estado', ['invitada', 'activa', 'suspendida'])->default('activa');
            $table->boolean('es_owner')->default(false);
            $table->foreignId('invitado_por')->nullable()
                ->constrained('usuarios', 'id_usuario')->nullOnDelete();
            $table->timestamp('unido_en')->nullable();
            $table->timestamps();

            $table->unique(['id_usuario', 'id_tenant']);
        });

        $this->backfillMembresias();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }

    /**
     * Una fila de membresía por cada usuario existente, apuntando a su
     * (único, por ahora) tenant. es_owner=true si ya es tenant.admin.
     */
    private function backfillMembresias(): void
    {
        $usuarios = DB::table('usuarios')->select('id_usuario', 'id_tenant', 'created_at')->get();

        $idsAdmin = DB::table('usuario_rol')
            ->join('roles', 'roles.id_rol', '=', 'usuario_rol.id_rol')
            ->where('roles.clave', 'tenant.admin')
            ->pluck('usuario_rol.id_usuario')
            ->all();

        foreach ($usuarios as $usuario) {
            DB::table('membresias')->insert([
                'id_usuario' => $usuario->id_usuario,
                'id_tenant' => $usuario->id_tenant,
                'estado' => 'activa',
                'es_owner' => in_array($usuario->id_usuario, $idsAdmin),
                'unido_en' => $usuario->created_at ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
