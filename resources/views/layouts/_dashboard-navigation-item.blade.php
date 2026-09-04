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
@endphp

@if($hasChildren)
<div class="nav-group {{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'open' : '' }}">
    <button type="button" class="nav-parent" aria-expanded="{{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'true' : 'false' }}">
        <span class="nav-icon"><i class="fa-solid fa-folder-tree"></i></span><span>{{ $item->displayLabel() }}</span><i class="fa-solid fa-chevron-down nav-chevron"></i>
    </button>
    <div class="nav-sub">
        @foreach($item->children as $child)
            @include('layouts._dashboard-navigation-item',['item'=>$child])
        @endforeach
    </div>
</div>
@else
<a class="{{ request()->url() === $dashboardUrl ? 'active' : '' }}"
   href="{{ $dashboardUrl }}"
   data-dashboard-link="{{ $item->route_name }}"
   @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid fa-circle-dot"></i></span><span>{{ $item->displayLabel() }}</span>
</a>
@endif
