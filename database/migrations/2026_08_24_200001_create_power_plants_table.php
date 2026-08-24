<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('power_plants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->decimal('capacity_kw', 14, 3)->nullable();
            $table->string('technology')->nullable();
            $table->string('status')->default('planned');
            $table->text('overview')->nullable();
            $table->date('started_at')->nullable();
            $table->date('commissioned_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'technology']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('power_plants');
    }
};
