<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dirección opcional. Mientras esté vacía, la página de contacto simplemente
 * no muestra la fila, sin anunciar la ausencia. Cuando el negocio tenga un
 * punto de atención, se llena desde el panel y aparece sola.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('address')->nullable()->after('facebook');
            $table->string('hours')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['address', 'hours']);
        });
    }
};
