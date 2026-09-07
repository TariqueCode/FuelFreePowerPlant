<?php

use App\Models\CmsPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The old Page Builder stored a legacy block/editor payload that is not
     * compatible with the new section-based builder. Start the new builder
     * with an intentionally empty page catalogue; website content is stored
     * separately and is therefore untouched.
     */
    public function up(): void
    {
        if (Schema::hasTable('cms_pages')) {
            CmsPage::query()->delete();
        }
    }

    public function down(): void
    {
        // Content removal is intentionally not reversible because the legacy
        // builder payload has been retired. Existing website content remains
        // available through SiteContentItem.
    }
};
