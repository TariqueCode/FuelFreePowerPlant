<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('label', 120);
            $table->string('description', 255)->nullable();
            $table->string('source_type', 40)->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        $defaults = [
            ['hero', 'Hero & Slider', 'The first visual visitors see.', 'slider'],
            ['welcome', 'Company Introduction', 'Your editable company welcome and vision.', 'cms_page'],
            ['statistics', 'Power Plant Statistics', 'Live figures calculated from plant records.', 'power_plants'],
            ['projects', 'Power Plant Projects', 'Published and operational projects from the plant register.', 'power_plants'],
            ['management', 'Management Team', 'Selected published leadership profiles.', 'management'],
            ['news', 'News & Notices', 'Published news and notices.', 'site_content'],
            ['gallery', 'Gallery', 'Published photo collections.', 'gallery'],
            ['cta', 'Contact & Call to Action', 'A clear next step for visitors.', 'system'],
        ];

        $savedOrder = [];
        if (Schema::hasTable('system_settings')) {
            $saved = DB::table('system_settings')
                ->whereIn('key', [
                    'home.section_order','home.hero_enabled','home.welcome_enabled','home.statistics_enabled',
                    'home.projects_enabled','home.news_enabled','home.gallery_enabled','home.cta_enabled',
                ])->pluck('value', 'key')->all();
            $decoded = json_decode($saved['home.section_order'] ?? '[]', true);
            if (is_array($decoded)) {
                $savedOrder = array_values(array_intersect($decoded, array_column($defaults, 0)));
            }
        }

        $order = $savedOrder ?: array_column($defaults, 0);
        $byKey = collect($defaults)->keyBy(fn ($item) => $item[0]);

        foreach ($order as $position => $key) {
            [$sectionKey, $label, $description, $sourceType] = $byKey[$key];
            $enabledKey = 'home.' . $sectionKey . '_enabled';
            $enabled = true;
            if (isset($saved[$enabledKey])) {
                $enabled = filter_var($saved[$enabledKey], FILTER_VALIDATE_BOOLEAN);
            }
            HomepageSection::query()->create([
                'key' => $sectionKey,
                'label' => $label,
                'description' => $description,
                'source_type' => $sourceType,
                'is_enabled' => $enabled,
                'sort_order' => $position,
            ]);
        }

        foreach ($byKey as $key => $definition) {
            if (!HomepageSection::query()->where('key', $key)->exists()) {
                [, $label, $description, $sourceType] = $definition;
                HomepageSection::query()->create([
                    'key' => $key,
                    'label' => $label,
                    'description' => $description,
                    'source_type' => $sourceType,
                    'is_enabled' => true,
                    'sort_order' => count($order),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
