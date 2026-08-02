<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enlace de la publicación del auto que el cliente quiere que revisemos.
 * En una revisión precompra es el dato más útil de todos: con la publicación
 * a la vista se sabe qué se está ofreciendo antes de ir a verlo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('t_listing_url', 500)->nullable()->after('t_plate');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('t_listing_url');
        });
    }
};
