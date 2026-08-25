<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            $table->string('address')->nullable()->after('location');
            $table->string('fuel_type', 120)->nullable()->after('technology');
            $table->string('ownership', 160)->nullable()->after('fuel_type');
            $table->string('operator', 160)->nullable()->after('ownership');
            $table->decimal('latitude', 10, 7)->nullable()->after('operator');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('annual_generation_mwh', 14, 3)->nullable()->after('capacity_kw');
            $table->decimal('efficiency_percent', 6, 3)->nullable()->after('annual_generation_mwh');
            $table->decimal('co2_reduction_tonnes', 14, 3)->nullable()->after('efficiency_percent');
            $table->decimal('land_area_acres', 12, 3)->nullable()->after('co2_reduction_tonnes');
            $table->string('image_path')->nullable()->after('overview');
            $table->string('contact_email')->nullable()->after('image_path');
            $table->string('contact_phone', 50)->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            $table->dropColumn(['address','fuel_type','ownership','operator','latitude','longitude','annual_generation_mwh','efficiency_percent','co2_reduction_tonnes','land_area_acres','image_path','contact_email','contact_phone']);
        });
    }
};
