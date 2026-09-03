@php
    $children = $menuItem->children ?? collect();
    $menuUrl = $menuItem->route_name && \Illuminate\Support\Facades\Route::has($menuItem->route_name)
        ? route($menuItem->route_name)
        : ($menuItem->url ?: '#');
@endphp
@if($children->isNotEmpty())
<div class="public-menu-dropdown" data-menu-id="{{ $menuItem->id }}">
    <button class="public-menu-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false">
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
<a href="{{ $menuUrl }}" target="{{ $menuItem->target }}" @if($menuItem->target==='_blank') rel="noopener noreferrer" @endif>
    @if($menuItem->icon)<i class="{{ $menuItem->icon }}" aria-hidden="true"></i>@endif {{ $menuItem->displayLabel() }}
</a>
@endif
