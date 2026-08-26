@php
$siteName = $brand['name'] ?? config('fuelfree.company.name');
$siteDomain = $brand['domain'] ?? config('fuelfree.company.domain');
@endphp
<footer class="site-footer">
  <div class="site-shell site-footer-grid">
    <div><div class="site-footer-brand">{{ $siteName }}</div><p>{{ $brand['tagline'] ?? config('fuelfree.company.tagline') }}</p></div>
    <div><strong>Explore</strong><a href="{{ route('site.about') }}">About</a><a href="{{ route('site.plants') }}">Power Plants</a><a href="{{ route('management') }}">Management</a><a href="{{ route('news.index') }}">News</a></div>
    <div><strong>Resources</strong><a href="{{ route('resources.index') }}">Resources</a><a href="{{ route('sustainability') }}">Sustainability</a><a href="{{ route('site.gallery') }}">Gallery</a><a href="{{ route('contact') }}">Contact</a></div>
  </div>
  <div class="site-shell site-footer-bottom"><span>© {{ date('Y') }} {{ $siteName }}. All rights reserved.</span><span>{{ $siteDomain }}</span></div>
</footer>
