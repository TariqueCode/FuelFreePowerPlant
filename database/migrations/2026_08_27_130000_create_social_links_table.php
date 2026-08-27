<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('label', 80);
            $table->string('url', 500);
            $table->string('icon', 100)->default('fa-brands fa-link');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $defaults = [
            ['Facebook', env('SOCIAL_FACEBOOK_URL'), 'fa-brands fa-facebook-f', 10],
            ['Instagram', env('SOCIAL_INSTAGRAM_URL'), 'fa-brands fa-instagram', 20],
            ['YouTube', env('SOCIAL_YOUTUBE_URL'), 'fa-brands fa-youtube', 30],
            ['LinkedIn', env('SOCIAL_LINKEDIN_URL'), 'fa-brands fa-linkedin-in', 40],
        ];

        foreach ($defaults as [$label, $url, $icon, $sortOrder]) {
            if (filled($url)) {
                \Illuminate\Support\Facades\DB::table('social_links')->insert([
                    'label' => $label,
                    'url' => $url,
                    'icon' => $icon,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
