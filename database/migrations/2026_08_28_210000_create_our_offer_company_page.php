<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_content_items')->updateOrInsert(
            ['type' => 'company', 'slug' => 'our-offer'],
            [
                'title' => 'Our Offer',
                'excerpt' => 'Powering Industry with a Smarter Energy Solution',
                'content' => <<<'HTML'
<h1>Powering Industry with a Smarter Energy Solution</h1>
<p>At FUEL FREE POWER PLANT LIMITED, we are redefining the way electricity can be generated and supplied.</p>
<p>Our Fuel-Free Flywheel-Based Clean Energy Technology is designed to provide a reliable, cost-effective and sustainable electricity solution for industries, commercial establishments, institutions and other power-intensive consumers.</p>
<p>We offer our customers an opportunity to access dependable electricity while reducing their reliance on conventional fuel-based power generation.</p>
<h2>Why Choose Our Power?</h2>
<h3>⚡ Fuel-Free Energy Solution</h3>
<p>Generate and use electricity without conventional fuel consumption, helping reduce exposure to fuel price volatility and fuel-related operating costs.</p>
<h3>💰 Cost-Effective Power</h3>
<p>Our electricity supply model is designed to provide competitive energy costs and improve the long-term economics of industrial operations.</p>
<h3>🔋 Reliable &amp; Continuous Supply</h3>
<p>A dependable power solution designed to support uninterrupted industrial and commercial operations.</p>
<h3>🌱 Clean &amp; Sustainable Energy</h3>
<p>Move towards a cleaner energy future with an innovative technology designed to minimize dependence on conventional fossil-fuel-based generation.</p>
<h3>🏭 Customized for Your Business</h3>
<p>Power capacity and supply arrangements can be structured according to your facility's electricity demand, operating profile and business requirements.</p>
<h3>🤝 Long-Term Energy Partnership</h3>
<p>We do not simply supply electricity—we aim to build long-term strategic energy partnerships with our customers.</p>
<h2>Our Value Proposition</h2>
<blockquote>More Reliable Power. Lower Fuel Dependency. Smarter Energy Economics.</blockquote>
<p>Whether you are operating a manufacturing plant, commercial facility, industrial complex, institution or other high-demand operation, FUEL FREE POWER PLANT LIMITED is committed to delivering a new generation of energy solutions tailored to your needs.</p>
<p><strong>Power Your Business. Reduce Fuel Dependency. Build a Sustainable Future.</strong></p>
<p>Partner with FUEL FREE POWER PLANT LIMITED for a smarter and more sustainable power solution.</p>
HTML,
                'status' => 'published',
                'published_at' => now(),
                'show_in_navigation' => true,
                'navigation_order' => 2,
                'sort_order' => 2,
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
            ->where('slug', 'our-offer')
            ->delete();
    }
};
