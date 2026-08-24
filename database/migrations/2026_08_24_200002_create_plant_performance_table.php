<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plant_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained('power_plants')->cascadeOnDelete();
            $table->dateTime('measured_at');
            $table->decimal('power_output_kw', 14, 3)->nullable();
            $table->decimal('energy_generated_kwh', 18, 3)->nullable();
            $table->decimal('efficiency_percent', 6, 3)->nullable();
            $table->decimal('uptime_percent', 6, 3)->nullable();
            $table->json('environmental_metrics')->nullable();
            $table->string('data_status')->default('demonstration');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['power_plant_id', 'measured_at']);
            $table->index('data_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_performance');
    }
};
