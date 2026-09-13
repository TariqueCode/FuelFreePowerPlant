@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=3">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=3">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=3">
<script src="{{ asset('admin-documents.js') }}?v=3" defer></script>
@endonce
<style>
@media (min-width: 901px){.sidebar .nav>a,.sidebar .nav-parent{font-size:15px!important}.sidebar .nav-sub a{font-size:14px!important}.sidebar .nav-label{font-size:10px!important}.content{font-size:15px!important}.content p,.content label,.content li,.content td,.content th,.content input,.content select,.content textarea,.content button,.content a,.content span,.content strong{font-size:15px!important}.content h2{font-size:22px!important}.content h3{font-size:19px!important}.content small{font-size:13px!important}.content .profile-info strong{font-size:15px!important}.content .profile-info span{font-size:13px!important}.content .folder-title h2{font-size:22px!important}.content .folder-title span,.content .folder-meta,.content .profile-status,.content .folder-status{font-size:13px!important}.content .hero p,.content .builder-note,.content .notice,.content .errors{font-size:14px!important}.content .primary,.content .secondary,.content .add-profile{font-size:14px!important}}
.nav-group{margin:2px 0 4px}.nav-group summary{list-style:none}.nav-group summary::-webkit-details-marker{display:none}.nav-group[open]>.nav-parent{background:rgba(67,194,229,.055);border-color:rgba(104,204,235,.07);color:#d9f1f5}.nav-group[open]>.nav-parent .nav-icon{color:#79ffb5!important}.nav-group[open]>.nav-parent .nav-chevron{transform:rotate(180deg)}.nav-parent{width:100%;min-height:42px;display:flex;align-items:center;gap:11px;padding:9px 11px;border:1px solid transparent;border-radius:12px;background:transparent;color:var(--muted);cursor:pointer;text-align:left;font-size:12px;user-select:none}.nav-parent>span:nth-child(2){flex:1}.nav-parent:hover{background:rgba(67,194,229,.055);border-color:rgba(104,204,235,.07);color:#d9f1f5}.nav-chevron{font-size:9px;transition:transform .18s}.nav-sub{display:none;margin:2px 0 5px 33px;padding-left:8px;border-left:1px solid rgba(104,204,235,.11)}.nav-group[open]>.nav-sub{display:flex;flex-direction:column;gap:2px}.nav-sub a{padding:8px 9px;border-radius:8px;color:#7898a5;text-decoration:none;font-size:10px}.nav-sub a:hover,.nav-sub a.active{color:#eaf8fb;background:rgba(67,194,229,.07)}.nav-parent:focus-visible,.nav-sub a:focus-visible,.nav>a:focus-visible,.profile-trigger:focus-visible,.mobile-menu-toggle:focus-visible{outline:2px solid #61d8f1;outline-offset:2px}.nav-sub a{min-width:0;display:block}.nav-label{user-select:none;padding:9px 9px 3px;color:#4f7180;font-size:8px;font-weight:700;letter-spacing:.13em}
@media(max-width:900px){.nav-group{display:block}.nav-sub{margin-left:27px}.nav-sub a{padding:10px 9px;font-size:11px}}
</style>
@php
$hasChildren=$item->children->isNotEmpty();
$isActive=request()->url()===$item->url;
$hasActiveDescendant=$hasChildren && $item->children->contains(function ($child): bool {
    if (request()->url()===$child->url) return true;
    return $child->children->isNotEmpty() && $child->children->contains(function ($nested): bool { return request()->url()===$nested->url; });
});
$navIcon=trim((string) $item->icon) !== '' ? trim($item->icon) : ($hasChildren ? 'fa-folder-tree' : 'fa-circle-dot');
@endphp
@if($hasChildren)
<details class="nav-group" {{ $hasActiveDescendant ? 'open' : '' }}>
    <summary class="nav-parent">
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