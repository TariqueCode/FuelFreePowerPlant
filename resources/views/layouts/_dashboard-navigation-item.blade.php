@once
<style>
.nav-group.nav-details > summary { list-style: none; }
.nav-group.nav-details > summary::-webkit-details-marker { display: none; }
.nav-group.nav-details[open] > .nav-sub { display: flex; flex-direction: column; gap: 2px; }
.nav-group.nav-details[open] > summary .nav-chevron { transform: rotate(180deg); }
</style>
@endonce

@php
    $hasChildren = $item->children->isNotEmpty();
    $builderRoutes = [
        'admin.profile-builder.index',
        'admin.page-builder.index',
        'admin.menu-builder.index',
    ];
    $isBuilderLink = in_array((string) $item->route_name, $builderRoutes, true);
    $dashboardUrl = $item->url;
    if ($isBuilderLink) {
        $dashboardUrl = route($item->route_name);
    }
    $isGroupOpen = $hasChildren && $item->children->contains(function ($child): bool {
        if ($child->children->isNotEmpty()) {
            return $child->children->contains(fn ($nested) => request()->url() === $nested->url);
        }
        return request()->url() === $child->url;
    });
@endphp

@if($hasChildren)
<details class="nav-group nav-details" @if($isGroupOpen) open @endif>
    <summary class="nav-parent">
        <span class="nav-icon"><i class="fa-solid fa-folder-tree"></i></span>
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
<a class="{{ request()->url() === $dashboardUrl ? 'active' : '' }}"
   href="{{ $dashboardUrl }}"
   data-dashboard-link="{{ $item->route_name }}"
   @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid fa-circle-dot"></i></span><span>{{ $item->displayLabel() }}</span>
</a>
@endif
