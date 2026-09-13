@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=2">
<script src="{{ asset('admin-documents.js') }}?v=2" defer></script>
@endonce
<style>
@media (min-width: 901px){.sidebar .nav>a,.sidebar .nav-parent{font-size:15px!important}.sidebar .nav-sub a{font-size:14px!important}.sidebar .nav-label{font-size:10px!important}.content{font-size:15px!important}.content p,.content label,.content li,.content td,.content th,.content input,.content select,.content textarea,.content button,.content a,.content span,.content strong{font-size:15px!important}.content h2{font-size:22px!important}.content h3{font-size:19px!important}.content small{font-size:13px!important}.content .profile-info strong{font-size:15px!important}.content .profile-info span{font-size:13px!important}.content .folder-title h2{font-size:22px!important}.content .folder-title span,.content .folder-meta,.content .profile-status,.content .folder-status{font-size:13px!important}.content .hero p,.content .builder-note,.content .notice,.content .errors{font-size:14px!important}.content .primary,.content .secondary,.content .add-profile{font-size:14px!important}}
</style>
@php
$hasChildren=$item->children->isNotEmpty();
$isActive=request()->url()===$item->url;
$hasActiveDescendant=$hasChildren && $item->children->contains(function ($child): bool {
    if (request()->url()===$child->url) return true;
    return $child->children->isNotEmpty() && $child->children->contains(function ($nested): bool { return request()->url()===$nested->url; });
});
$navOpen=$hasActiveDescendant;
$navIcon=trim((string) $item->icon) !== '' ? trim($item->icon) : ($hasChildren ? 'fa-folder-tree' : 'fa-circle-dot');
@endphp
@if($hasChildren)
<div class="nav-group {{ $navOpen?'open':'' }}">
    <button type="button" class="nav-parent" aria-expanded="{{ $navOpen?'true':'false' }}" aria-controls="dashboard-nav-group-{{ $item->id }}">
        <span class="nav-icon"><i class="fa-solid {{ $navIcon }}"></i></span>
        <span>{{ $item->displayLabel() }}</span>
        <i class="fa-solid fa-chevron-down nav-chevron"></i>
    </button>
    <div class="nav-sub" id="dashboard-nav-group-{{ $item->id }}">
        @foreach($item->children as $child)
            @include('layouts._dashboard-navigation-item',['item'=>$child])
        @endforeach
    </div>
</div>
@else
<a class="{{ $isActive?'active':'' }}" href="{{ $item->url }}" @if($item->target==='_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid {{ $navIcon }}"></i></span>
    <span>{{ $item->displayLabel() }}</span>
</a>
@endif

@once
<script>
(function () {
    function initDashboardNavigationGroups() {
        document.querySelectorAll('.nav-parent').forEach(function (button) {
            if (button.dataset.navBound === '1') return;
            button.dataset.navBound = '1';
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var group = button.closest('.nav-group');
                if (!group) return;
                var open = !group.classList.contains('open');
                group.classList.toggle('open', open);
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardNavigationGroups, { once: true });
    } else {
        initDashboardNavigationGroups();
    }
})();
</script>
@endonce