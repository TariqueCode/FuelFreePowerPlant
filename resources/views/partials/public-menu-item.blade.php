@php
    $children = $menuItem->children ?? collect();
    $depth = (int) ($depth ?? 0);
    // Prefer the resolved live URL. This is essential for parameterized routes
    // such as company.page, where route($routeName) alone cannot generate a URL
    // without the required slug parameter.
    $menuUrl = filled($menuItem->url)
        ? $menuItem->url
        : ($menuItem->route_name && \Illuminate\Support\Facades\Route::has($menuItem->route_name)
            ? route($menuItem->route_name)
            : '#');
@endphp

@if($depth === 0)
<style>
    /* Navigation typography: every menu level uses one consistent text size. */
    .public-header-nav .public-menu > a,
    .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle,
    .public-header-nav .public-menu-dropdown-panel a,
    .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-toggle {
        font-size:14px !important;
        line-height:1.3 !important;
    }

    /* Compact responsive tree: hierarchy is created by indentation, not oversized cards. */
    @media (max-width:720px) {
        .public-header-nav .public-menu { gap:4px !important; }

        .public-header-nav .public-menu > a,
        .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle {
            width:100% !important;
            min-height:44px !important;
            height:44px !important;
            padding:0 13px !important;
            border-radius:10px !important;
            box-sizing:border-box !important;
            font-size:14px !important;
            font-weight:600 !important;
        }

        .public-header-nav .public-menu > .public-menu-dropdown {
            display:block !important;
            width:100% !important;
            margin:0 !important;
            position:relative !important;
        }

        .public-header-nav .public-menu-dropdown-panel {
            position:static !important;
            width:auto !important;
            min-width:0 !important;
            max-width:none !important;
            margin:4px 0 0 calc(7px + (var(--menu-depth, 0) * 7px)) !important;
            padding:2px 0 2px 10px !important;
            background:transparent !important;
            border:0 !important;
            border-left:1px solid rgba(86,210,238,.24) !important;
            border-radius:0 !important;
            box-shadow:none !important;
            transform:none !important;
            opacity:1 !important;
            visibility:visible !important;
        }

        .public-header-nav .public-menu-dropdown-panel { display:none !important; }
        .public-header-nav .public-menu-dropdown.is-open > .public-menu-dropdown-panel { display:block !important; }

        .public-header-nav .public-menu-dropdown-panel > a,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
            display:flex !important;
            align-items:center !important;
            justify-content:flex-start !important;
            width:100% !important;
            min-height:36px !important;
            height:auto !important;
            margin:1px 0 !important;
            padding:7px 9px !important;
            box-sizing:border-box !important;
            border:0 !important;
            border-radius:7px !important;
            background:transparent !important;
            color:#a7c0c8 !important;
            font-size:14px !important;
            font-weight:500 !important;
            line-height:1.3 !important;
            text-align:left !important;
        }

        .public-header-nav .public-menu-dropdown-panel > a::before,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle::before {
            content:"";
            width:4px;
            height:4px;
            flex:0 0 4px;
            margin-right:9px;
            border-radius:50%;
            background:rgba(81,216,240,.6);
        }

        .public-header-nav .public-menu-dropdown-panel > a:hover,
        .public-header-nav .public-menu-dropdown-panel > a:focus,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:hover,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:focus,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] {
            background:rgba(67,209,240,.07) !important;
            color:#effcff !important;
            outline:none;
        }

        .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown {
            display:block !important;
            width:100% !important;
            position:static !important;
        }

        .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown > .public-menu-dropdown-toggle {
            width:100% !important;
        }

        .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-chevron {
            width:7px;
            height:7px;
            flex:0 0 7px;
            margin-left:auto;
        }
    }

    @media (min-width:721px) {
        /* Desktop hierarchy: each level has a predictable panel and a clear parent/child relationship. */
        .public-header-nav .public-menu-dropdown-panel {
            width:250px !important;
            min-width:250px !important;
            max-width:min(340px,calc(100vw - 24px)) !important;
            padding:9px !important;
            border-color:rgba(87,230,255,.22) !important;
            box-shadow:0 22px 60px rgba(0,0,0,.48),0 0 32px rgba(87,230,255,.055) !important;
        }

        .public-header-nav .public-menu-dropdown-panel > a,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
            position:relative;
            min-height:40px !important;
            margin:2px 0 !important;
            padding:9px 11px !important;
            border:1px solid transparent !important;
            border-radius:9px !important;
            font-size:13px !important;
            font-weight:650 !important;
            color:#b8ced5 !important;
            box-sizing:border-box !important;
            transition:color .18s ease,background .18s ease,border-color .18s ease,box-shadow .18s ease !important;
        }

        .public-header-nav .public-menu-dropdown-panel > a:hover,
        .public-header-nav .public-menu-dropdown-panel > a:focus-visible,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:hover,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:focus-visible,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown:focus-within > .public-menu-dropdown-toggle,
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown.is-open > .public-menu-dropdown-toggle {
            color:#effcff !important;
            background:linear-gradient(135deg,rgba(87,230,255,.105),rgba(57,230,166,.055)) !important;
            border-color:rgba(87,230,255,.16) !important;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.035) !important;
            outline:none !important;
        }

        /* Only root-level folders open on hover. Nested folders stay click-only. */
        .public-header-nav > .public-menu > .public-menu-dropdown > .public-menu-dropdown-panel .public-menu-dropdown:hover > .public-menu-dropdown-panel{
            opacity:0 !important;
            visibility:hidden !important;
            pointer-events:none !important;
        }

        .public-header-nav > .public-menu > .public-menu-dropdown > .public-menu-dropdown-panel .public-menu-dropdown.is-open > .public-menu-dropdown-panel{
            opacity:1 !important;
            visibility:visible !important;
            pointer-events:auto !important;
            transform:translate(0,0) !important;
        }

        /* Child folders behave like real hierarchy nodes, not like unrelated links. */
        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown {
            position:relative !important;
            display:block !important;
            width:100% !important;
            margin:2px 0 !important;
        }

        .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
            width:100% !important;
            justify-content:space-between !important;
        }

        .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel {
            top:-9px !important;
            left:100% !important;
            margin-left:8px !important;
            width:250px !important;
            min-width:250px !important;
            max-height:min(70vh,520px) !important;
            overflow-y:auto !important;
            overflow-x:visible !important;
            padding:9px !important;
            border-left:2px solid rgba(87,230,255,.22) !important;
        }

        .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel::before {
            content:"";
            position:absolute;
            left:-8px;
            top:18px;
            width:7px;
            height:1px;
            background:rgba(87,230,255,.32);
            pointer-events:none;
        }

        /* Focus and hover should expose the same hierarchy for mouse and keyboard users. */
        .public-header-nav .public-menu-dropdown.is-open > .public-menu-dropdown-panel {
            opacity:1 !important;
            visibility:visible !important;
            pointer-events:auto !important;
            transform:translate(0,0) !important;
        }

        .public-header-nav > .public-menu > .public-menu-dropdown.is-open > .public-menu-dropdown-panel {
            transform:translate(-50%,0) !important;
        }

        /* Keep the first-level panel centered while nested panels open beside their parent. */
        .public-header-nav > .public-menu > .public-menu-dropdown > .public-menu-dropdown-panel {
            left:50% !important;
            top:100% !important;
            margin-left:0 !important;
        }
    }
</style>
@endif

@if($children->isNotEmpty())
<div class="public-menu-dropdown public-menu-level-{{ $depth }}" data-menu-id="{{ $menuItem->id }}" style="--menu-depth:{{ $depth }};">
    <button class="public-menu-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" style="font-size:14px !important;">
        @if($menuItem->icon)<i class="{{ $menuItem->icon }}" aria-hidden="true"></i>@endif
        <span>{{ $menuItem->displayLabel() }}</span><span class="public-menu-dropdown-chevron" aria-hidden="true"></span>
    </button>
    <div class="public-menu-dropdown-panel" style="--menu-depth:{{ $depth + 1 }};">
        @foreach($children as $child)
            @include('partials.public-menu-item',['menuItem'=>$child, 'depth'=>$depth + 1])
        @endforeach
    </div>
</div>
@else
<a href="{{ $menuUrl }}" target="{{ $menuItem->target }}" @if($menuItem->target==='_blank') rel="noopener noreferrer" @endif style="font-size:14px !important;">
    @if($menuItem->icon)<i class="{{ $menuItem->icon }}" aria-hidden="true"></i>@endif {{ $menuItem->displayLabel() }}
</a>
@endif
