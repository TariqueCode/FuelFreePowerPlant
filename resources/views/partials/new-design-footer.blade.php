@php
    $newSocialLinks = $newSocialLinks ?? collect();
    $newCompanyName = $brand['name'] ?? config('fuelfree.company.name', 'FuelFree PowerPlant');
    $newLogo = !empty($brand['logo_path']) ? asset('storage/'.ltrim($brand['logo_path'], '/')) : null;
    $newTagline = $brand['tagline'] ?? config('fuelfree.company.tagline', 'Powering a cleaner, smarter future.');
@endphp
<footer class="new-footer">
    <div class="new-container">
        <div class="new-footer-grid">
            <div class="new-footer-brand">
                <a class="new-brand" href="/" aria-label="{{ $newCompanyName }} home">
                    <span class="new-brand-mark">@if($newLogo)<img src="{{ $newLogo }}" alt="">@else<span aria-hidden="true">FF</span>@endif</span>
                    <span class="new-brand-name">FuelFree <small>POWER PLANT LIMITED</small></span>
                </a>
                <p>{{ $newTagline }}</p>
                @if($newSocialLinks->isNotEmpty())
                    <div class="new-socials" aria-label="Social media">
                        @foreach($newSocialLinks as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label ?: $social->platform }}">{{ $social->label ?: ucfirst($social->platform) }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div><h3>Explore</h3><a href="/about-us">About</a><a href="/solutions">Solutions</a><a href="/news">News & Events</a><a href="/gallery">Gallery</a></div>
            <div><h3>Corporate</h3><a href="/management">Management</a><a href="/career">Careers</a><a href="/contact">Contact</a><a href="/sustainability">Sustainability</a></div>
            <div><h3>Resources</h3><a href="/mail">Webmail</a><a href="/login">Client / Admin Login</a><a href="/pages/technology">Technology</a><a href="/contact">Technical Inquiry</a></div>
        </div>
        <div class="new-footer-bottom"><span>© {{ date('Y') }} {{ $newCompanyName }}. All rights reserved.</span><span>FuelFree PowerPlant · Energy technology platform</span></div>
    </div>
</footer>
