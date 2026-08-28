<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_content_items')->updateOrInsert(
            ['type' => 'company', 'slug' => 'profit-analysis'],
            [
                'title' => 'Profit Analysis',
                'excerpt' => 'Financial analysis and scenario comparison for the 1 MW Flywheel-Based Clean Energy Power Plant.',
                'content' => <<<'HTML'
<h1>Profit Analysis</h1>
<p>This section presents the financial analysis framework for the 1 MW Flywheel-Based Clean Energy Power Plant.</p>

<h2 id="aggressive">Aggressive</h2>
<p>Aggressive operating scenario. Final financial figures and assumptions will be published after confirmation of the approved client source.</p>

<h2 id="normal">Normal</h2>
<p>Normal operating scenario. Final financial figures and assumptions will be published after confirmation of the approved client source.</p>

<h2 id="conservative">Conservative</h2>
<p>Conservative operating scenario. Final financial figures and assumptions will be published after confirmation of the approved client source.</p>

<h2 id="five-year-cash-flow">5-Year Cash Flow</h2>
<p>Five-year cash-flow comparison for the Aggressive, Normal and Conservative scenarios.</p>

<h2 id="cumulative-operating-cash-flow">Cumulative Operating Cash Flow</h2>
<p>Five-year cumulative operating cash-flow presentation across the three financial scenarios.</p>

<h2 id="scenario-comparison">Scenario Comparison</h2>
<p>Comparative view of the three financial scenarios, including their assumptions and projected results.</p>
HTML,
                'status' => 'draft',
                'published_at' => null,
                'show_in_navigation' => false,
                'navigation_order' => null,
                'sort_order' => 4,
                'is_featured' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('site_content_items')
            ->where('type', 'company')
            ->where('slug', 'profit-analysis')
            ->delete();
    }
};
