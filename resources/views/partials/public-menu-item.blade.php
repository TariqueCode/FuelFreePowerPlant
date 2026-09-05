@php
    $children = $menuItem->children ?? collect();
    // Prefer the resolved live URL. This is essential for parameterized routes
    // such as company.page, where route($routeName) alone cannot generate a URL
    // without the required slug parameter.
    $menuUrl = filled($menuItem->url)
        ? $menuItem->url
        : ($menuItem->route_name && \Illuminate\Support\Facades\Route::has($menuItem->route_name)
            ? route($menuItem->route_name)
            : '#');
@endphp
@if($children->isNotEmpty())
<div class="public-menu-dropdown" data-menu-id="{{ $menuItem->id }}">
    <button class="public-menu-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" style="font-size:14px !important;">
        @if($menuItem->icon)<i class="{{ $menuItem->icon }}" aria-hidden="true"></i>@endif
        <span>{{ $menuItem->displayLabel() }}</span><span class="public-menu-dropdown-chevron" aria-hidden="true"></span>
    </button>
    <div class="public-menu-dropdown-panel">
        @foreach($children as $child)
            @include('partials.public-menu-item',['menuItem'=>$child])
        @endforeach
    </div>
</div>
@else
<a href="{{ $menuUrl }}" target="{{ $menuItem->target }}" @if($menuItem->target==='_blank') rel="noopener noreferrer" @endif style="font-size:14px !important;">
    @if($menuItem->icon)<i class="{{ $menuItem->icon }}" aria-hidden="true"></i>@endif {{ $menuItem->displayLabel() }}
</a>
@endif
