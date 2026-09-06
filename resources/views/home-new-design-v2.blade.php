@php
    $logo = !empty($brand['logo_path']) ? asset('storage/'.ltrim($brand['logo_path'], '/')) : null;
    $companyName = $brand['name'] ?? 'Fuel Free Power Plant Limited';
    $tagline = $brand['tagline'] ?? 'Advanced energy technology for a stronger future.';
    $heroSlider = $sliders->first();
    $heroImage = $heroSlider?->image_path ? asset('storage/'.ltrim($heroSlider->image_path, '/')) : null;
    $newsItems = collect($content['news'] ?? [])->take(3);
    $projects = collect($projects ?? [])->take(2);
    $leaders = collect($homeManagement ?? [])->take(4);
    $galleryItems = collect($gallery ?? [])->take(4);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#050b12">
    <meta name="description" content="{{ $tagline }}">
    <meta property="og:title" content="{{ $companyName }} — Energy Technology">
    <meta property="og:description" content="{{ $tagline }}">
    @if($heroImage)<meta property="og:image" content="{{ $heroImage }}">@endif
    <title>{{ $companyName }} — Energy Technology</title>
    @vite(['resources/css/new-design.css','resources/css/new-shell.css','resources/js/new-design.js'])
</head>
<body>
<div class="new-site">
    @include('partials.new-design-header', ['brand'=>$brand, 'newNavigation'=>$newNavigation])
    <main>
        <section class="new-hero{{ $heroImage ? ' has-media' : '' }}" @if($heroImage) style="--hero-media:url('{{ $heroImage }}')" @endif>
            <div class="new-container new-hero-grid">
                <div class="new-reveal">
                    <span class="new-eyebrow">Energy technology · engineering · innovation</span>
                    <h1>Building the next <span class="new-gradient-text">energy future.</span></h1>
                    <p class="new-hero-copy">{{ $tagline }} We are shaping a technology-led energy platform around engineering discipline, measurable performance, reliability and long-term impact.</p>
                    <div class="new-actions">
                        <a class="new-btn-primary" href="/solutions">Explore solutions <span aria-hidden="true">→</span></a>
                        <a class="new-btn-secondary" href="#technology">Discover the technology</a>
                    </div>
                    <div class="new-hero-meta" aria-label="Company focus areas">
                        <span><strong>01</strong> Engineering</span>
                        <span><strong>02</strong> Reliability</span>
                        <span><strong>03</strong> Future-ready</span>
                    </div>
                </div>
                <div class="new-hero-visual new-reveal" aria-label="Energy technology visualization">
                    <div class="new-energy-core">
                        <div class="new-core-center"><div><strong>FFP</strong><span>Energy platform</span></div></div>
                    </div>
                    <div class="new-float-card one"><strong>Engineering</strong><span>Technology first</span></div>
                    <div class="new-float-card two"><strong>Future ready</strong><span>Built to scale</span></div>
                    @if($heroImage)<div class="new-hero-source"><span>Featured visual</span><strong>{{ $heroSlider->title ?: 'FuelFree PowerPlant' }}</strong></div>@endif
                </div>
            </div>
        </section>

        <section class="new-section new-section-intro" id="technology">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">The platform</span><h2>Technology with a clear purpose.</h2></div>
                    <p>Technical claims remain evidence-led, with verification state made explicit instead of implied.</p>
                </div>
                <div class="new-grid-3">
                    <article class="new-card new-reveal"><div class="new-card-icon">01</div><h3>Advanced Engineering</h3><p>Explain complex energy technology through architecture, components, energy flow and control concepts.</p><a class="new-card-link" href="/company/our-technology">Explore technology <span aria-hidden="true">↗</span></a></article>
                    <article class="new-card new-reveal"><div class="new-card-icon">02</div><h3>Measured Performance</h3><p>Present performance through traceable records, timestamps, data sources and explicit verification state.</p><a class="new-card-link" href="/plants">View facilities <span aria-hidden="true">↗</span></a></article>
                    <article class="new-card new-reveal"><div class="new-card-icon">03</div><h3>Future Telemetry</h3><p>Keep the platform ready for future plant data integration without forcing a rewrite of the core application.</p><a class="new-card-link" href="/sustainability">Our impact <span aria-hidden="true">↗</span></a></article>
                </div>
            </div>
        </section>

        <section class="new-section new-section-alt" id="solutions">
            <div class="new-container new-feature">
                <div class="new-panel new-reveal">
                    <span class="new-eyebrow">Energy flow</span><h2>From concept to controlled output.</h2>
                    <p>The engineering story stays understandable without turning the experience into a noisy dashboard.</p>
                    <div class="new-data-list">
                        <div class="new-data-row"><span>Technology status</span><strong>Evidence-led</strong></div>
                        <div class="new-data-row"><span>Performance state</span><strong>Explicit</strong></div>
                        <div class="new-data-row"><span>Data architecture</span><strong>Telemetry-ready</strong></div>
                    </div>
                </div>
                <div class="new-panel new-tech-map new-reveal">
                    <div class="new-flow" aria-label="Energy flow concept">
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Technology Input</strong><span>Defined engineering system</span></div></div>
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Conversion &amp; Control</strong><span>Monitored operating layer</span></div></div>
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Useful Energy Output</strong><span>Measured where a real source exists</span></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="new-section" id="projects">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">Projects &amp; Our Plans</span><h2>Where the platform meets reality.</h2></div>
                    <p>Current projects and future plans use managed content while operational measurements remain governed separately.</p>
                </div>
                <div class="new-projects">
                    @forelse($projects as $project)
                        @php $projectImage = $project->image_path ? asset('storage/'.ltrim($project->image_path, '/')) : null; @endphp
                        <a class="new-project-card new-reveal{{ $projectImage ? ' has-media' : '' }}" href="{{ $project->slug ? '/company/'.$project->slug : '/plants' }}" @if($projectImage) style="--project-media:url('{{ $projectImage }}')" @endif>
                            <span class="new-eyebrow">{{ $project->type === 'plants' ? 'Facility' : 'Project' }}</span>
                            <h3>{{ $project->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($project->content ?? $project->excerpt ?? ''),180) }}</p>
                            <span class="new-project-arrow" aria-hidden="true">↗</span>
                        </a>
                    @empty
                        <article class="new-project-card new-reveal"><span class="new-eyebrow">Projects</span><h3>Engineering the roadmap.</h3><p>Project records will appear here from the managed content source.</p></article>
                        <article class="new-project-card new-reveal"><span class="new-eyebrow">Facilities</span><h3>Plants &amp; performance.</h3><p>Approved facility and performance records will become the authoritative public source.</p></article>
                    @endforelse
                </div>
                <div class="new-section-link new-reveal"><a href="/plants">View Projects &amp; Our Plans <span aria-hidden="true">→</span></a></div>
            </div>
        </section>

        @if($leaders->isNotEmpty())
        <section class="new-section new-section-alt" id="leadership">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">Leadership</span><h2>People behind the platform.</h2></div>
                    <p>Management profiles are drawn from the authoritative content source so leadership information stays consistent across the website.</p>
                </div>
                <div class="new-leadership-grid">
                    @foreach($leaders as $leader)
                        @php $leaderImage = $leader->image_path ? asset('storage/'.ltrim($leader->image_path, '/')) : null; @endphp
                        <a class="new-leader-card new-reveal" href="{{ $leader->slug ? '/company/'.$leader->slug : '/management' }}">
                            <div class="new-leader-media">@if($leaderImage)<img src="{{ $leaderImage }}" alt="{{ $leader->title }}" loading="lazy">@else<span aria-hidden="true">FFP</span>@endif</div>
                            <div class="new-leader-copy"><span>{{ $leader->designation ?: 'Management' }}</span><h3>{{ $leader->title }}</h3><strong>View profile <span aria-hidden="true">↗</span></strong></div>
                        </a>
                    @endforeach
                </div>
                <div class="new-section-link new-reveal"><a href="/management">Meet the full leadership team <span aria-hidden="true">→</span></a></div>
            </div>
        </section>
        @endif

        @if($galleryItems->isNotEmpty())
        <section class="new-section" id="gallery">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">Visual record</span><h2>Inside FuelFree PowerPlant.</h2></div>
                    <p>A curated visual layer for facilities, engineering work, corporate moments and approved media.</p>
                </div>
                <div class="new-gallery-grid">
                    @foreach($galleryItems as $item)
                        @if($item->image_path)
                            <a class="new-gallery-card new-reveal" href="{{ $item->slug ? '/gallery/'.$item->slug : '/gallery' }}"><img src="{{ asset('storage/'.ltrim($item->image_path, '/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy"><span>{{ $item->title }}</span></a>
                        @endif
                    @endforeach
                </div>
                <div class="new-section-link new-reveal"><a href="/gallery">Open the full gallery <span aria-hidden="true">→</span></a></div>
            </div>
        </section>
        @endif

        <section class="new-section new-section-alt">
            <div class="new-container new-news">
                <article class="new-panel new-news-main new-reveal"><span class="new-eyebrow">Corporate platform</span><h2>One system. Clear information.</h2><p>The new website is being rebuilt as a scalable corporate platform with authoritative modules for public content, technology, projects, management and resources.</p><div class="new-actions"><a class="new-btn-primary" href="/about-us">About the company <span aria-hidden="true">→</span></a></div></article>
                <div class="new-news-list">
                    @forelse($newsItems as $item)
                        <a class="new-news-item new-reveal" href="{{ $item->slug ? '/news/'.$item->slug : '/news' }}"><span class="new-news-kicker">{{ $item->type === 'announcement' ? 'Announcement' : 'News' }}</span><strong>{{ $item->title }}</strong><span>{{ optional($item->published_at)->format('d M Y') ?? 'Latest update' }}</span></a>
                    @empty
                        <a class="new-news-item new-reveal" href="/news"><span class="new-news-kicker">Updates</span><strong>News &amp; Events</strong><span>View the latest corporate updates</span></a>
                        <a class="new-news-item new-reveal" href="/gallery"><span class="new-news-kicker">Media</span><strong>Gallery</strong><span>Explore company media and moments</span></a>
                        <a class="new-news-item new-reveal" href="/career"><span class="new-news-kicker">People</span><strong>Careers</strong><span>Build the future with us</span></a>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="new-cta new-reveal"><div><span class="new-eyebrow">Start a conversation</span><h2>Let’s build a stronger energy future.</h2></div><div class="new-actions"><a class="new-btn-primary" href="/contact">Contact FuelFree PowerPlant <span aria-hidden="true">→</span></a></div></section>
    </main>
    @include('partials.new-design-footer', ['brand'=>$brand, 'newSocialLinks'=>$newSocialLinks])
</div>
</body>
</html>
