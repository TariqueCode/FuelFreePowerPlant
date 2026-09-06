@php
    $newNavigation = $newNavigation ?? collect();
    $newCompanyName = $brand['name'] ?? config('fuelfree.company.name', 'FuelFree PowerPlant');
    $newLogo = !empty($brand['logo_path']) ? asset('storage/'.ltrim($brand['logo_path'], '/')) : null;
@endphp
<header class="new-nav" data-new-header>
    <div class="new-container new-nav-inner">
        <a class="new-brand" href="/" aria-label="{{ $newCompanyName }} home">
            <span class="new-brand-mark">
                @if($newLogo)<img src="{{ $newLogo }}" alt="{{ $newCompanyName }} logo">@else<span aria-hidden="true">FF</span>@endif
            </span>
            <span class="new-brand-name">FuelFree <small>POWER PLANT LIMITED</small></span>
        </a>
        <button class="new-nav-toggle" type="button" data-new-nav-toggle aria-expanded="false" aria-controls="new-primary-navigation" aria-label="Open navigation"><span aria-hidden="true">☰</span></button>
        <nav class="new-nav-links" id="new-primary-navigation" data-new-nav-links aria-label="Primary navigation">
            @forelse($newNavigation as $item)
                @if($item->children->isNotEmpty())
                    <div class="new-nav-group">
                        <button type="button" class="new-nav-link new-nav-dropdown-toggle" data-new-nav-dropdown aria-expanded="false">{{ $item->displayLabel() }} <span aria-hidden="true">⌄</span></button>
                        <div class="new-nav-dropdown" hidden>
                            @foreach($item->children as $child)
                                @if($child->is_visible)
                                    <a href="{{ $child->url ?: '#' }}" target="{{ $child->target ?: '_self' }}">{{ $child->displayLabel() }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->url ?: '#' }}" target="{{ $item->target ?: '_self' }}">{{ $item->displayLabel() }}</a>
                @endif
            @empty
                <a href="/about-us">About</a><a href="/solutions">Solutions</a><a href="/news">News</a><a href="/contact">Contact</a>
            @endforelse
            <a class="new-mobile-cta" href="/contact">Talk to us</a>
        </nav>
        <a class="new-nav-cta new-desktop-cta" href="/contact">Talk to us</a>
    </div>
</header>
