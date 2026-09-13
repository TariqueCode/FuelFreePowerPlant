@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=4">
<script src="{{ asset('admin-documents.js') }}?v=5" defer></script>
@endonce
<style>
.sidebar .nav>a,
.sidebar .nav-parent{box-sizing:border-box;width:100%;min-height:44px;display:flex!important;align-items:center;gap:11px;padding:9px 11px;border:1px solid transparent;border-radius:12px;background:transparent;color:var(--muted);text-decoration:none;font-size:15px!important;font-weight:500;line-height:1.2;white-space:nowrap;text-align:left;cursor:pointer}
.sidebar .nav>a:hover,
.sidebar .nav-parent:hover{background:rgba(67,194,229,.055);border-color:rgba(104,204,235,.07);color:#d9f1f5}
.sidebar .nav>a.active{background:linear-gradient(90deg,rgba(67,194,229,.13),rgba(67,194,229,.045));border-color:rgba(104,204,235,.11);color:var(--text);box-shadow:inset 3px 0 0 #49c8e6}
.sidebar .nav-icon{width:22px!important;height:22px;display:grid!important;place-items:center;flex:0 0 22px;color:#6e9fac}
.sidebar .nav>a.active .nav-icon,
.sidebar .nav>a:hover .nav-icon,
.sidebar .nav-group[open]>.nav-parent .nav-icon{color:#72d8ef!important}
.sidebar .nav-icon i{font-size:15px!important;line-height:1}
.nav-group{margin:2px 0 4px}
.nav-group summary{list-style:none}
.nav-group summary::-webkit-details-marker{display:none}
.nav-group[open]>.nav-parent{background:rgba(67,194,229,.055);border-color:rgba(104,204,235,.07);color:#d9f1f5}
.nav-group[open]>.nav-parent .nav-icon{color:#79ffb5!important}
.nav-group[open]>.nav-parent .nav-chevron{transform:rotate(180deg)}
.nav-parent>span:nth-child(2){min-width:0;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.nav-chevron{flex:0 0 auto;font-size:10px!important;transition:transform .18s}
.nav-sub{display:none;margin:2px 0 5px 33px;padding-left:8px;border-left:1px solid rgba(104,204,235,.11)}
.nav-group[open]>.nav-sub{display:flex;flex-direction:column;gap:2px}
.nav-sub a{min-width:0;display:flex!important;align-items:center;gap:10px;padding:8px 9px;border-radius:8px;color:#7898a5;text-decoration:none;font-size:14px!important;line-height:1.2;white-space:nowrap}
.nav-sub a:hover,.nav-sub a.active{color:#eaf8fb;background:rgba(67,194,229,.07)}
.nav-sub a .nav-icon{width:18px!important;height:18px;flex-basis:18px}
.nav-sub a .nav-icon i{font-size:13px!important}
.nav-label{user-select:none;padding:9px 9px 3px;color:#4f7180;font-size:10px!important;font-weight:700;letter-spacing:.13em}
.nav-parent:focus-visible,.nav-sub a:focus-visible,.nav>a:focus-visible,.profile-trigger:focus-visible,.mobile-menu-toggle:focus-visible{outline:2px solid #61d8f1;outline-offset:2px}
@media(max-width:900px){
    .sidebar .nav>a,.sidebar .nav-parent{font-size:16px!important;min-height:46px}
    .nav-sub{margin-left:27px}
    .nav-sub a{padding:10px 9px;font-size:14px!important}
}
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