<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $content = <<<'HTML'
<h2>Fuel Free Power Plant Limited</h2>
<p>Fuel Free Power Plant Limited is an innovative clean energy company committed to developing and promoting fuel-free, flywheel-based power generation and energy technology. Our vision is to contribute to a cleaner, more sustainable and energy-secure future by developing advanced power systems that can reduce dependence on conventional fossil fuels.</p>
<p>Our core technology is based on a flywheel-driven power generation system, designed to utilize mechanical energy and advanced engineering principles to produce electricity without continuous dependence on diesel, gas or other conventional fuels during operation. The technology has been developed with the objective of providing a reliable, cost-effective and environmentally responsible alternative for power generation.</p>
<h2>Our Vision</h2>
<p>Our vision is to help create a future where reliable electricity can be generated with significantly lower dependence on conventional fuels, reducing energy costs, environmental pollution and the pressure on limited natural resources.</p>
<p>We believe that the future of energy will require innovative technologies that combine economic efficiency, environmental sustainability and energy security. Fuel Free Power Plant Limited is working toward that future through research, development, engineering and practical implementation of flywheel-based clean energy systems.</p>
<h2>Our Technology</h2>
<p>Our Fuel-Free Flywheel-Based Clean Energy Technology is intended to provide electricity for a wide range of applications, including industrial facilities, commercial establishments, backup power systems, charging stations and other electricity-intensive operations.</p>
<p>The system is being developed in scalable capacities so that it can potentially serve both small and large energy requirements. Our long-term objective is to develop power plants capable of supplying electricity directly to industrial users as well as contributing electricity to the national power grid, subject to applicable technical, regulatory and grid-integration requirements.</p>
<h2>Our Mission</h2>
<ul><li>Develop and commercialize fuel-free flywheel-based power technology.</li><li>Reduce dependence on diesel, gas and other conventional fuels for electricity generation.</li><li>Provide reliable and economically competitive electricity solutions for industries and businesses.</li><li>Promote clean and environmentally responsible energy generation.</li><li>Support Bangladesh's growing demand for reliable and affordable electricity.</li><li>Develop scalable power-generation systems suitable for both industrial and grid-connected applications.</li><li>Encourage research, innovation and engineering excellence in the clean energy sector.</li></ul>
<h2>A New Approach to Power Generation</h2>
<p>The conventional power-generation sector remains heavily dependent on fossil fuels. Fuel costs, price fluctuations, transportation requirements and environmental impacts create significant challenges for industries and energy consumers.</p>
<p>Fuel Free Power Plant Limited seeks to address these challenges through a fundamentally different approach—reducing the need for continuous fuel consumption in power generation through flywheel-based mechanical energy technology.</p>
<p>Our approach is not limited to developing a single power plant. We aim to establish a complete technological and industrial ecosystem involving research and development, engineering, manufacturing, installation, operation, maintenance and large-scale deployment.</p>
<h2>Our Commitment</h2>
<p>We are committed to developing our technology through continuous research, testing, technical evaluation and engineering improvement. We recognize that innovative energy technology must be demonstrated, independently evaluated and continuously improved before large-scale commercial deployment.</p>
<p>Therefore, our development process emphasizes technical validation, safety, reliability, efficiency and practical performance.</p>
<p>We also seek cooperation with researchers, engineers, industrial organizations, investors, financial institutions, policymakers and energy-sector stakeholders to accelerate the development and deployment of our technology.</p>
<h2>Looking Ahead</h2>
<p>Fuel Free Power Plant Limited aims to become a significant contributor to the development of clean, affordable and sustainable energy solutions in Bangladesh and, ultimately, in international markets.</p>
<p>Our long-term goal is to establish a new generation of power plants that can support industries, businesses and communities while reducing dependence on conventional fuels and contributing to a cleaner environment.</p>
<p><strong>Fuel Free Power Plant Limited — advancing toward a fuel-free, cleaner and more sustainable energy future.</strong></p>
HTML;

        DB::table('site_content_items')->updateOrInsert(
            ['type'=>'company','slug'=>'about-us'],
            ['title'=>'About Us','excerpt'=>'Fuel Free Power Plant Limited is an innovative clean energy company committed to developing and promoting fuel-free, flywheel-based power generation and energy technology.','content'=>$content,'status'=>'published','sort_order'=>1,'published_at'=>now(),'updated_at'=>now(),'created_at'=>now()]
        );
        DB::table('cms_pages')->where('slug','about-us')->delete();
    }

    public function down(): void
    {
        DB::table('site_content_items')->where('type','company')->where('slug','about-us')->delete();
    }
};
