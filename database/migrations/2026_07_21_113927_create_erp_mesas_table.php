<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_mesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tenant')->constrained('tenants', 'id_tenant')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->unsignedInteger('capacidad')->default(2);
            $table->string('estado')->default('libre');
            $table->string('mesero')->nullable();
            $table->timestamps();

            $table->unique(['id_tenant', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_mesas');
    }
};
