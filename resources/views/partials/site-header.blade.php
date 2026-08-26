@php
$siteName = $brand['name'] ?? config('fuelfree.company.name');
$siteLogo = $brand['logo_path'] ?? null;
@endphp
<header class="site-header">
  <div class="site-shell site-nav">
    <a class="site-brand" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
      @if($siteLogo)<img src="{{ asset('storage/'.ltrim($siteLogo,'/')) }}" alt="{{ $siteName }}">@else<span class="site-brand-fallback"><i class="fa-solid fa-bolt"></i></span>@endif
      <span class="site-brand-name">{{ $siteName }}</span>
    </a>
    <nav class="site-links" aria-label="Primary navigation">
      <a href="{{ route('home') }}">Home</a><a href="{{ route('site.about') }}">About</a><a href="{{ route('site.plants') }}">Power Plants</a><a href="{{ route('management') }}">Management</a><a href="{{ route('news.index') }}">News</a><a href="{{ route('resources.index') }}">Resources</a><a href="{{ route('sustainability') }}">Sustainability</a><a href="{{ route('contact') }}">Contact</a>
    </nav>
    <button class="site-menu-btn" type="button" aria-label="Open navigation" aria-expanded="false" data-site-menu><i class="fa-solid fa-bars"></i></button>
  </div>
  <nav class="site-mobile-links" data-site-mobile-menu aria-label="Mobile navigation">
    <a href="{{ route('home') }}">Home</a><a href="{{ route('site.about') }}">About</a><a href="{{ route('site.plants') }}">Power Plants</a><a href="{{ route('management') }}">Management</a><a href="{{ route('news.index') }}">News</a><a href="{{ route('resources.index') }}">Resources</a><a href="{{ route('sustainability') }}">Sustainability</a><a href="{{ route('contact') }}">Contact</a>
  </nav>
</header>
