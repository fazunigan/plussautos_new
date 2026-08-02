<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos que el cotizador necesita para que la oferta salga ajustada a la
 * primera. Sin versión, estado y comuna hay que pedirlos por WhatsApp igual,
 * y se pierde el punto de que el formulario sea rápido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('t_version')->nullable()->after('t_model');
            $table->string('t_condition')->nullable()->after('t_mileage_km');
            $table->string('t_comuna')->nullable()->after('t_condition');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['t_version', 't_condition', 't_comuna']);
        });
    }
};
