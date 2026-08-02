<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('message')->nullable();

            // Datos del vehículo que el dueño quiere vender (leads de tasación)
            $table->string('t_brand')->nullable();
            $table->string('t_model')->nullable();
            $table->unsignedSmallInteger('t_year')->nullable();
            $table->unsignedInteger('t_mileage_km')->nullable();
            $table->string('t_plate')->nullable();

            $table->string('status')->default('nuevo');
            $table->string('source')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
