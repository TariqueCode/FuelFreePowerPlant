<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!HomepageSection::query()->where('key', 'highlight')->exists()) {
            HomepageSection::query()->create([
                'key' => 'highlight',
                'label' => 'Homepage Highlight',
                'description' => 'The announcement banner shown when visitors enter the website.',
                'source_type' => 'site_popup',
                'is_enabled' => true,
                'sort_order' => (int) HomepageSection::query()->max('sort_order') + 1,
            ]);
        }
    }

    public function down(): void
    {
        HomepageSection::query()->where('key', 'highlight')->delete();
    }
};
