@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-ui-polish.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('admin-global-system.css') }}?v=1">

<script src="{{ asset('admin-documents.js') }}?v=6" defer></script>
<script src="{{ asset('admin-navigation-fix.js') }}?v=4" defer></script>
@endonce

@php
$hasChildren=$item->children->isNotEmpty();
$matchesCurrent=function ($candidate): bool {
    if ($candidate->route_name && request()->routeIs($candidate->route_name)) return true;
    return rtrim(request()->fullUrl(), '/') === rtrim(url((string) $candidate->url), '/');
};
$isActive=!$hasChildren && $matchesCurrent($item);
$hasActiveDescendant=$hasChildren && $item->children->contains(function ($child) use ($matchesCurrent): bool {
    if ($matchesCurrent($child)) return true;
    return $child->children->isNotEmpty() && $child->children->contains(fn ($nested): bool => $matchesCurrent($nested));
});
$navIcon=trim((string) $item->icon) !== '' ? trim($item->icon) : ($hasChildren ? 'fa-folder-tree' : 'fa-circle-dot');
$navKey=$hasChildren ? sha1((string) $item->url.'|'.(string) $item->displayLabel()) : null;
@endphp
@if($hasChildren)
<details class="nav-group" data-nav-key="{{ $navKey }}" {{ $hasActiveDescendant ? 'open' : '' }}>
    <summary class="nav-parent" aria-expanded="{{ $hasActiveDescendant ? 'true' : 'false' }}">
        <span class="nav-icon"><i class="fa-solid {{ $navIcon }}"></i></span>
        <span>{{ $item->displayLabel() }}</span>
        <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i>
    </summary>
    <div class="nav-sub">
        @foreach($item->children as $child)
            @include('layouts._dashboard-navigation-item',['item'=>$child])
        @endforeach
    </div>
</details>
@else
<a class="{{ $isActive?'active':'' }}" href="{{ $item->url }}" @if($item->target==='_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid {{ $navIcon }}"></i></span>
    <span>{{ $item->displayLabel() }}</span>
</a>
@endif