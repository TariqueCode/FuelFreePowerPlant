@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-ui-polish.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('admin-global-system.css') }}?v=1">
<style>
/* FuelFree PowerPlant — canonical admin navigation system.
   One visual language, route-aware active state, zero-motion, responsive. */
.sidebar .nav>a,
.sidebar .nav-parent{
    box-sizing:border-box!important;
    width:100%!important;
    min-height:44px!important;
    display:flex!important;
    align-items:center!important;
    gap:11px!important;
    padding:9px 11px!important;
    border:1px solid transparent!important;
    border-radius:11px!important;
    background:transparent!important;
    color:#89a9a8!important;
    text-decoration:none!important;
    font-size:14px!important;
    font-weight:600!important;
    line-height:1.2!important;
    white-space:nowrap!important;
    text-align:left!important;
    cursor:pointer!important;
    transform:none!important;
    transition:none!important;
    animation:none!important;
}
.sidebar .nav>a:hover,
.sidebar .nav-parent:hover{
    background:rgba(50,229,138,.055)!important;
    border-color:rgba(85,217,239,.10)!important;
    color:#eefcf8!important;
    transform:none!important;
}
.sidebar .nav>a.active{
    background:linear-gradient(100deg,rgba(50,229,138,.18),rgba(85,217,239,.075))!important;
    border-color:rgba(50,229,138,.23)!important;
    color:#f3fffb!important;
    font-weight:800!important;
    box-shadow:inset 3px 0 0 #32e58a!important;
}
.sidebar .nav-icon{
    width:22px!important;
    height:22px!important;
    display:grid!important;
    place-items:center!important;
    flex:0 0 22px!important;
    color:#66a8a7!important;
}
.sidebar .nav>a.active .nav-icon,
.sidebar .nav>a:hover .nav-icon,
.sidebar .nav-group[open]>.nav-parent .nav-icon{
    color:#79ffb5!important;
}
.sidebar .nav-icon i{font-size:15px!important;line-height:1!important}
.nav-group{margin:2px 0 4px!important}
.nav-group summary{list-style:none!important}
.nav-group summary::-webkit-details-marker{display:none!important}
.nav-group[open]>.nav-parent{
    background:rgba(50,229,138,.065)!important;
    border-color:rgba(50,229,138,.13)!important;
    color:#eefcf8!important;
    box-shadow:none!important;
}
.nav-group[open]>.nav-parent .nav-chevron{transform:rotate(180deg)!important}
.nav-parent>span:nth-child(2){
    min-width:0!important;
    flex:1!important;
    overflow:hidden!important;
    text-overflow:ellipsis!important;
    white-space:nowrap!important;
}
.nav-chevron{
    flex:0 0 auto!important;
    font-size:9px!important;
    transform:none!important;
    transition:none!important;
    animation:none!important;
}
.nav-sub{
    display:none;
    margin:3px 0 6px 33px!important;
    padding-left:9px!important;
    border-left:1px solid rgba(85,217,239,.13)!important;
}
.nav-group[open]>.nav-sub{
    display:flex!important;
    flex-direction:column!important;
    gap:2px!important;
}
.nav-sub a{
    min-width:0!important;
    display:flex!important;
    align-items:center!important;
    gap:9px!important;
    min-height:37px!important;
    padding:8px 9px!important;
    border:1px solid transparent!important;
    border-radius:9px!important;
    color:#789b9a!important;
    background:transparent!important;
    text-decoration:none!important;
    font-size:12px!important;
    font-weight:550!important;
    line-height:1.2!important;
    white-space:nowrap!important;
    transform:none!important;
    transition:none!important;
    animation:none!important;
}
.nav-sub a:hover{
    background:rgba(50,229,138,.055)!important;
    border-color:rgba(85,217,239,.08)!important;
    color:#eafcf7!important;
}
.nav-sub a.active{
    background:linear-gradient(100deg,rgba(50,229,138,.18),rgba(85,217,239,.075))!important;
    border-color:rgba(50,229,138,.22)!important;
    color:#f3fffb!important;
    font-weight:800!important;
    box-shadow:inset 2px 0 0 #32e58a!important;
}
.nav-sub a.active .nav-icon{color:#79ffb5!important}
.nav-sub a .nav-icon{width:18px!important;height:18px!important;flex-basis:18px!important}
.nav-sub a .nav-icon i{font-size:13px!important}
.nav-label{
    user-select:none!important;
    padding:9px 9px 3px!important;
    color:#527776!important;
    font-size:9px!important;
    font-weight:800!important;
    letter-spacing:.14em!important;
}
.nav-parent:focus-visible,
.nav-sub a:focus-visible,
.nav>a:focus-visible,
.profile-trigger:focus-visible,
.mobile-menu-toggle:focus-visible{
    outline:2px solid #55d9ef!important;
    outline-offset:2px!important;
}
/* No motion: fast, deterministic navigation. */
.sidebar,
.sidebar *,
.mobile-drawer-backdrop,
.mobile-menu-toggle,
.profile-trigger{
    animation:none!important;
    transition:none!important;
}
@media(max-width:900px){
    .sidebar .nav>a,
    .sidebar .nav-parent{
        min-height:46px!important;
        font-size:15px!important;
    }
    .nav-sub{margin-left:27px!important}
    .nav-sub a{min-height:39px!important;padding:10px 9px!important;font-size:13px!important}
}
@media(max-width:520px){
    .sidebar{width:min(86vw,292px)!important}
    .sidebar .nav>a,
    .sidebar .nav-parent{font-size:15px!important}
    .nav-sub a{font-size:13px!important}
}
@media(prefers-reduced-motion:reduce){
    .sidebar,.sidebar *,.mobile-drawer-backdrop{animation:none!important;transition:none!important;scroll-behavior:auto!important}
}
</style>
<script src="{{ asset('admin-documents.js') }}?v=6" defer></script>
<script src="{{ asset('admin-navigation-fix.js') }}?v=4" defer></script>
@endonce
@php
$hasChildren=$item->children->isNotEmpty();
$matchesCurrent=function ($candidate): bool {
    if ($candidate->route_name) {
        $routeName=(string) $candidate->route_name;
        if (request()->routeIs($routeName)) return true;
        if (str_ends_with($routeName,'.index') && request()->routeIs(substr($routeName,0,-6).'*')) return true;
    }
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