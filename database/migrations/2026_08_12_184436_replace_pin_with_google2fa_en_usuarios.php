<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('pin');
            // Secreto TOTP (cifrado por el cast 'encrypted' en el modelo)
            // para el login rápido de cajero con una app tipo Google
            // Authenticator, en reemplazo del PIN numérico. Null = este
            // usuario no tiene 2FA configurado todavía. text() y no
            // string(): el payload cifrado (JSON+base64 de Laravel) es
            // bastante más largo que los ~32 caracteres del secreto plano.
            $table->text('google2fa_secret')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('google2fa_secret');
            $table->string('pin')->nullable()->after('password');
        });
    }
};
