<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id('id_suscripcion');
            $table->foreignId('id_tenant')
                ->constrained('tenants', 'id_tenant')
                ->cascadeOnDelete();
            $table->foreignId('id_plan')
                ->constrained('plans', 'id_plan');

            $table->string('stripe_subscription_id')->unique();
            $table->string('stripe_price_id');
            // activa | periodo_gracia | cancelada | vencida | incompleta
            $table->string('estado', 30)->default('incompleta');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin_periodo_actual')->nullable();
            $table->boolean('cancela_al_final_periodo')->default(false);
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->json('ultimo_evento_stripe')->nullable();
            $table->timestamps();

            $table->index(['id_tenant', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
