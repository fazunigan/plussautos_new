<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_model_id')->constrained()->restrictOnDelete();

            // Datos públicos
            $table->string('version')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('mileage_km');
            $table->string('transmission');
            $table->string('fuel');
            $table->string('body_type');
            $table->unsignedSmallInteger('engine_cc')->nullable();
            $table->unsignedTinyInteger('doors')->nullable();
            $table->string('traction')->nullable();
            $table->string('color')->nullable();
            $table->unsignedTinyInteger('owners_count')->nullable();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();

            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->boolean('featured')->default(false);

            // Datos internos. Nunca se exponen en el sitio público.
            $table->string('origin')->default('own');
            $table->string('plate')->nullable();
            $table->string('consignor_name')->nullable();
            $table->string('consignor_phone')->nullable();
            $table->unsignedBigInteger('purchase_price')->nullable();
            $table->unsignedBigInteger('commission_amount')->nullable();
            $table->string('location')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('year');
            $table->index('price');
            $table->index('mileage_km');
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
