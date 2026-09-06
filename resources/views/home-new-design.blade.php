@php
    $logo = !empty($brand['logo_path']) ? asset('storage/'.ltrim($brand['logo_path'], '/')) : null;
    $companyName = $brand['name'] ?? 'Fuel Free Power Plant Limited';
    $tagline = $brand['tagline'] ?? 'Advanced energy technology for a stronger future.';
    $newsItems = collect($content['news'] ?? [])->take(3);
    $projects = collect($content['future-project'] ?? [])->take(2);
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050b12">
    <meta name="description" content="{{ $tagline }}">
    <title>{{ $companyName }} — Energy Technology</title>
    @vite(['resources/css/new-design.css', 'resources/js/new-design.js'])
</head>
<body>
<div class="new-site">
    <header class="new-nav">
        <div class="new-container new-nav-inner">
            <a class="new-brand" href="/" aria-label="{{ $companyName }} home">
                <span class="new-brand-mark">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $companyName }} logo">
                    @else
                        <span aria-hidden="true">FF</span>
                    @endif
                </span>
                <span class="new-brand-name">FuelFree <small>POWER PLANT LIMITED</small></span>
            </a>

            <nav class="new-nav-links" data-new-nav-links aria-label="Primary navigation">
                <a href="#technology">Technology</a>
                <a href="#solutions">Solutions</a>
                <a href="#projects">Projects</a>
                <a href="/about-us">About</a>
                <a href="/news">News</a>
                <a href="/contact">Contact</a>
            </nav>

            <a class="new-nav-cta" href="/contact">Talk to us</a>
            <button class="new-nav-toggle" type="button" data-new-nav-toggle aria-expanded="false" aria-controls="primary-navigation" aria-label="Open navigation">
                <span aria-hidden="true">☰</span>
            </button>
        </div>
    </header>

    <main>
        <section class="new-hero">
            <div class="new-container new-hero-grid">
                <div class="new-reveal">
                    <span class="new-eyebrow">Energy technology · engineering · innovation</span>
                    <h1>Building the next <span class="new-gradient-text">energy future.</span></h1>
                    <p class="new-hero-copy">{{ $tagline }} We are shaping a technology-led energy platform around engineering discipline, measurable performance, reliability and long-term impact.</p>
                    <div class="new-actions">
                        <a class="new-btn-primary" href="/solutions">Explore solutions →</a>
                        <a class="new-btn-secondary" href="#technology">Discover the technology</a>
                    </div>
                </div>

                <div class="new-hero-visual new-reveal" aria-label="Energy technology visualization">
                    <div class="new-energy-core">
                        <div class="new-core-center">
                            <div><strong>FFP</strong><span>Energy platform</span></div>
                        </div>
                    </div>
                    <div class="new-float-card one"><strong>Engineering</strong><span>Technology first</span></div>
                    <div class="new-float-card two"><strong>Future ready</strong><span>Built to scale</span></div>
                </div>
            </div>
        </section>

        <section class="new-section" id="technology">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">The platform</span><h2>Technology with a clear purpose.</h2></div>
                    <p>Every public technical claim should be evidence-led. Verified, estimated, demonstration, target and real-time states remain explicit throughout the platform.</p>
                </div>
                <div class="new-grid-3">
                    <article class="new-card new-reveal"><div class="new-card-icon">01</div><h3>Advanced Engineering</h3><p>Present complex energy technology through clear architecture, components, energy flow and control concepts.</p></article>
                    <article class="new-card new-reveal"><div class="new-card-icon">02</div><h3>Measured Performance</h3><p>Performance information is designed around traceable records, timestamps, data sources and explicit verification state.</p></article>
                    <article class="new-card new-reveal"><div class="new-card-icon">03</div><h3>Future Telemetry</h3><p>A telemetry-ready boundary allows future plant data integration without forcing a rewrite of the core application.</p></article>
                </div>
            </div>
        </section>

        <section class="new-section" id="solutions">
            <div class="new-container new-feature">
                <div class="new-panel new-reveal">
                    <span class="new-eyebrow">Energy flow</span>
                    <h2>From concept to controlled output.</h2>
                    <p>The new experience makes the engineering story understandable without turning the interface into a dashboard full of noise.</p>
                    <div class="new-data-list">
                        <div class="new-data-row"><span>Technology status</span><strong>Evidence-led</strong></div>
                        <div class="new-data-row"><span>Performance state</span><strong>Explicit</strong></div>
                        <div class="new-data-row"><span>Data architecture</span><strong>Telemetry-ready</strong></div>
                    </div>
                </div>
                <div class="new-panel new-tech-map new-reveal">
                    <div class="new-flow" aria-label="Energy flow concept">
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Technology Input</strong><span>Defined engineering system</span></div></div>
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Conversion & Control</strong><span>Monitored operating layer</span></div></div>
                        <div class="new-flow-node"><i class="new-flow-dot"></i><div><strong>Useful Energy Output</strong><span>Measured where a real source exists</span></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="new-section" id="projects">
            <div class="new-container">
                <div class="new-section-head new-reveal">
                    <div><span class="new-eyebrow">Projects & our plans</span><h2>Where the platform meets reality.</h2></div>
                    <p>Projects, plants and future plans will share a consistent content model, while operational measurements remain separate and governed.</p>
                </div>
                <div class="new-projects">
                    @forelse($projects as $project)
                        <article class="new-project-card new-reveal"><span class="new-eyebrow">Project</span><h3>{{ $project->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($project->content ?? $project->excerpt ?? ''), 180) }}</p></article>
                    @empty
                        <article class="new-project-card new-reveal"><span class="new-eyebrow">Projects</span><h3>Engineering the roadmap.</h3><p>Project records will appear here directly from the managed project content source—no duplicate homepage content required.</p></article>
                        <article class="new-project-card new-reveal"><span class="new-eyebrow">Facilities</span><h3>Plants & performance.</h3><p>Approved facility and performance records will become the authoritative source for public plant information.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="new-section">
            <div class="new-container new-news">
                <article class="new-panel new-news-main new-reveal">
                    <span class="new-eyebrow">Corporate platform</span>
                    <h2>One system. Clear information.</h2>
                    <p>The new website is being rebuilt as a scalable corporate platform—not as a visual patch on the legacy interface. Public content, projects, technology, management and resources will be managed through authoritative modules.</p>
                    <div class="new-actions"><a class="new-btn-primary" href="/about-us">About the company →</a></div>
                </article>
                <div class="new-news-list">
                    @forelse($newsItems as $item)
                        <a class="new-news-item new-reveal" href="{{ $item->slug ? '/news/'.$item->slug : '/news' }}"><strong>{{ $item->title }}</strong><span>{{ optional($item->published_at)->format('d M Y') ?? 'Latest update' }}</span></a>
                    @empty
                        <a class="new-news-item new-reveal" href="/news"><strong>News & Events</strong><span>View the latest corporate updates</span></a>
                        <a class="new-news-item new-reveal" href="/gallery"><strong>Gallery</strong><span>Explore company media and moments</span></a>
                        <a class="new-news-item new-reveal" href="/career"><strong>Careers</strong><span>Build the future with us</span></a>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="new-cta new-reveal">
            <div><span class="new-eyebrow">Start a conversation</span><h2>Let’s build a stronger energy future.</h2></div>
            <div class="new-actions"><a class="new-btn-primary" href="/contact">Contact FuelFree PowerPlant →</a></div>
        </section>
    </main>

    <footer class="new-footer">
        <div class="new-container">
            <div class="new-footer-grid">
                <div class="new-footer-brand">
                    <div class="new-brand"><span class="new-brand-mark">@if($logo)<img src="{{ $logo }}" alt="">@else<span>FF</span>@endif</span><span class="new-brand-name">FuelFree <small>POWER PLANT LIMITED</small></span></div>
                    <p>{{ $tagline }}</p>
                </div>
                <div><h3>Explore</h3><a href="/about-us">About</a><a href="/solutions">Solutions</a><a href="/news">News & Events</a><a href="/gallery">Gallery</a></div>
                <div><h3>Corporate</h3><a href="/management">Management</a><a href="/career">Careers</a><a href="/contact">Contact</a><a href="/pages/technology">Technology</a></div>
                <div><h3>Services</h3><a href="/mail">Webmail</a><a href="/login">Client / Admin Login</a><a href="/sustainability">Sustainability</a></div>
            </div>
            <div class="new-footer-bottom"><span>© {{ date('Y') }} {{ $companyName }}. All rights reserved.</span><span>New website design system · Staging-first development</span></div>
        </div>
    </footer>
</div>
</body>
</html>
