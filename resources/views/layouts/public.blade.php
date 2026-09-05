@php
    $useGlobalFramework = $useGlobalFramework ?? true;
    $useGlobalHeader = $useGlobalFramework && ($useGlobalHeader ?? true);
    $useGlobalFooter = $useGlobalFramework && ($useGlobalFooter ?? true);
    $publicBrand = $brand ?? null;
    if (!$publicBrand || (is_countable($publicBrand) && count($publicBrand) === 0)) {
        $publicBrand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    }
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $themeSettings = \App\Models\SystemSetting::query()->whereIn('key',['theme.primary','theme.accent','theme.background','theme.surface','theme.text','theme.muted','theme.max_width','theme.radius','theme.base_font'])->pluck('value','key');
    $theme = ['primary'=>$themeSettings->get('theme.primary','#48d8f1'),'accent'=>$themeSettings->get('theme.accent','#72dfbf'),'background'=>$themeSettings->get('theme.background','#031018'),'surface'=>$themeSettings->get('theme.surface','#071b26'),'text'=>$themeSettings->get('theme.text','#effcff'),'muted'=>$themeSettings->get('theme.muted','#91aeb8'),'max_width'=>$themeSettings->get('theme.max_width','1280px'),'radius'=>$themeSettings->get('theme.radius','16px'),'base_font'=>$themeSettings->get('theme.base_font','Inter, system-ui, sans-serif')];
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    if (!$publicLogo) {
        $publicLogo = \App\Models\SystemSetting::query()->where('key','company.logo_path')->value('value');
    }
@endphp
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', $publicName)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @php($faviconVersion = $publicLogo ? sha1((string) $publicLogo) : 'default-v1')
    <link rel="icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <link rel="shortcut icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <style>
        :root{--public-max:{{$theme['max_width']}};--public-gutter:16px;--public-bg:{{$theme['background']}};--public-surface:{{$theme['surface']}};--public-line:color-mix(in srgb, {{$theme['primary']}} 18%, transparent);--public-text:{{$theme['text']}};--public-muted:{{$theme['muted']}};--public-accent:{{$theme['primary']}};--public-radius:{{$theme['radius']}};--public-space-1:4px;--public-space-2:8px;--public-space-3:12px;--public-space-4:16px;--public-space-5:24px;--public-space-6:32px;--public-space-7:48px;--public-space-8:64px}
        .public-container{width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto}
        html{font-size:16px;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        body{margin:0;font-size:1rem;line-height:1.6;background:linear-gradient(180deg,{{$theme['background']}} 0%,{{$theme['surface']}} 52%,{{$theme['background']}} 100%);color:{{$theme['text']}};min-height:100vh;font-family:{{$theme['base_font']}};font-weight:400;letter-spacing:0}
        button,input,select,textarea{font:inherit}
        @media(max-width:600px){body{font-size:1rem}}
    </style>
    @if(request()->routeIs('home'))
        <style>
            @media (max-width: 650px) {
                .home-profile-modal .home-profile-links > .home-profile-link[href^="tel:"],
                .home-profile-modal .home-profile-links > .home-profile-link[href^="mailto:"] { display:none!important; }
                .home-profile-modal .home-profile-links { grid-template-columns:1fr!important;width:100%;max-width:100%;margin-inline:auto; }
                .home-profile-modal .home-profile-links > .home-profile-link.primary { grid-column:1/-1!important;width:100%;min-height:44px;margin-inline:auto; }
            }

            /* Desktop homepage only: use the entire content area for two wide leadership cards. */
            @media (min-width: 992px) {
                html body main.shell.home-v3 .home-section-management .management-grid {
                    display:grid !important;
                    grid-template-columns:repeat(2,minmax(0,1fr)) !important;
                    gap:24px !important;
                    width:100% !important;
                    max-width:none !important;
                    margin-left:0 !important;
                    margin-right:0 !important;
                    justify-content:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-card {
                    display:grid !important;
                    grid-template-columns:minmax(0,40%) minmax(0,1fr) !important;
                    width:100% !important;
                    min-width:0 !important;
                    min-height:0 !important;
                    height:auto !important;
                    align-items:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-photo {
                    width:100% !important;
                    height:auto !important;
                    min-height:0 !important;
                    aspect-ratio:4 / 5 !important;
                    align-self:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-photo img {
                    width:100% !important;
                    height:100% !important;
                    aspect-ratio:4 / 5 !important;
                    object-fit:cover !important;
                }
                html body main.shell.home-v3 .home-section-management .member-body {
                    min-width:0 !important;
                    min-height:0 !important;
                    height:100% !important;
                    padding:22px !important;
                }
            }
        </style>
    @endif
    @stack('head')
    <style>
        /* Responsive navigation tree: compact rows, clear hierarchy, no mobile fly-outs. */
        @media (max-width:720px) {
            .public-header-nav .public-menu { gap:5px!important; }
            .public-header-nav .public-menu > .public-menu-dropdown { display:block!important;width:100%!important;margin:0!important;position:relative!important; }
            .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle,
            .public-header-nav .public-menu > a { width:100%!important;min-height:46px!important;height:46px!important;box-sizing:border-box!important;border-radius:11px!important;padding:0 13px!important; }
            .public-header-nav .public-menu-dropdown-panel,
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel {
                position:static!important;inset:auto!important;transform:none!important;width:auto!important;min-width:0!important;max-width:none!important;
                margin:4px 0 0 9px!important;padding:0 0 0 10px!important;background:transparent!important;border:0!important;border-left:1px solid rgba(86,210,238,.22)!important;border-radius:0!important;box-shadow:none!important;
            }
            .public-header-nav .public-menu-dropdown-panel { display:none;opacity:1!important;visibility:visible!important;pointer-events:auto!important; }
            .public-header-nav .public-menu-dropdown.is-open > .public-menu-dropdown-panel { display:block!important; }
            .public-header-nav .public-menu-dropdown-panel > a,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
                display:flex!important;align-items:center!important;width:100%!important;min-height:38px!important;height:auto!important;box-sizing:border-box!important;
                justify-content:flex-start!important;margin:2px 0!important;padding:8px 10px!important;border:0!important;border-radius:7px!important;background:transparent!important;color:#9db8c1!important;font-size:13px!important;line-height:1.3!important;text-align:left!important;
            }
            .public-header-nav .public-menu-dropdown-panel > a::before,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle::before { content:"";width:5px;height:5px;flex:0 0 5px;margin-right:9px;border-radius:50%;background:rgba(81,216,240,.55); }
            .public-header-nav .public-menu-dropdown-panel > a:hover,
            .public-header-nav .public-menu-dropdown-panel > a:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:hover,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] { background:rgba(67,209,240,.07)!important;color:#effcff!important;outline:none; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown { display:block!important;width:100%!important;position:static!important; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown > .public-menu-dropdown-toggle { width:100%!important; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-chevron { flex:0 0 auto;width:7px;height:7px;margin-left:auto; }
            .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] { background:rgba(67,209,240,.09)!important;border-color:rgba(86,210,238,.17)!important;color:#effcff!important; }
            .public-header-nav .mobile-portal-separator { display:block!important;height:1px!important;margin:14px 8px 10px!important;background:linear-gradient(90deg,transparent 0%,rgba(86,210,238,.16) 16%,rgba(86,210,238,.42) 50%,rgba(86,210,238,.16) 84%,transparent 100%)!important; }
            .public-header-nav .mobile-menu-portal { position:relative!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:10px!important;width:calc(100% - 8px)!important;min-height:52px!important;height:52px!important;box-sizing:border-box!important;margin:0 4px 4px!important;padding:0 17px!important;overflow:hidden!important;border:1px solid rgba(98,217,238,.38)!important;border-radius:13px!important;background:linear-gradient(135deg,rgba(72,216,241,.14),rgba(72,216,241,.045) 55%,rgba(114,223,191,.055))!important;color:#effcff!important;font-size:15px!important;line-height:1!important;font-weight:800!important;letter-spacing:.01em!important;text-decoration:none!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 8px 22px rgba(0,0,0,.16)!important;isolation:isolate;transition:border-color .18s ease,background .18s ease,transform .18s ease,box-shadow .18s ease!important; }
            .public-header-nav .mobile-menu-portal::before { content:"";position:absolute;inset:0;pointer-events:none;z-index:-1;background:radial-gradient(circle at 50% -80%,rgba(98,217,238,.26),transparent 58%); }
            .public-header-nav .mobile-menu-portal::after { content:"";position:absolute;left:14px;right:14px;top:0;height:1px;pointer-events:none;background:linear-gradient(90deg,transparent,rgba(255,255,255,.32),transparent);opacity:.55; }
            .public-header-nav .mobile-menu-portal i { display:grid!important;place-items:center!important;width:27px!important;height:27px!important;flex:0 0 27px!important;border:1px solid rgba(139,243,255,.28)!important;border-radius:8px!important;background:rgba(4,18,26,.42)!important;color:#8bf3ff!important;font-size:12px!important;box-shadow:0 0 0 3px rgba(86,210,238,.035)!important; }
            .public-header-nav .mobile-menu-portal span { position:relative!important;top:0!important; }
            .public-header-nav .mobile-menu-portal:hover,
            .public-header-nav .mobile-menu-portal:focus-visible { background:linear-gradient(135deg,rgba(72,216,241,.19),rgba(72,216,241,.07) 55%,rgba(114,223,191,.08))!important;border-color:rgba(98,217,238,.58)!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 10px 28px rgba(0,0,0,.22),0 0 0 3px rgba(86,210,238,.055)!important;outline:none!important; }
            .public-header-nav .mobile-menu-portal:active { transform:translateY(1px)!important; }
        }
        @media (prefers-reduced-motion:reduce) { .public-header-nav .mobile-menu-portal { transition:none!important; } }
        @media (min-width:721px) { .public-header-nav .public-menu-dropdown-panel { max-width:min(320px,calc(100vw - 24px)); } .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel { max-height:min(70vh,520px);overflow-y:auto; } }
    </style>
</head>
<body>
    @if($useGlobalHeader) @include('partials.public-header', ['brand' => $publicBrand]) @endif
    @yield('content')
    @if($useGlobalFooter) @include('partials.public-footer', ['brand' => $publicBrand]) @endif
    @stack('scripts')
    <script>
        (() => {
            const cleanEscapedNewlines = () => {
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
                const nodes = [];
                while (walker.nextNode()) nodes.push(walker.currentNode);
                nodes.forEach(node => {
                    if (!node.nodeValue) return;
                    const cleaned = node.nodeValue.replaceAll('\\n', '').trim();
                    if (cleaned === '') node.remove();
                    else if (cleaned !== node.nodeValue.trim()) node.nodeValue = cleaned;
                });
            };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', cleanEscapedNewlines, { once:true });
            else cleanEscapedNewlines();
        })();
    </script>
</body>
</html>
