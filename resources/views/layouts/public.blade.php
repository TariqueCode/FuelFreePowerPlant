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
    // Some pages pass a partial brand array. Always recover the global logo so
    // the favicon stays identical across every public page.
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
    {{-- The favicon endpoint always resolves to the current admin-managed company logo. --}}
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
            /* Homepage: mobile leadership popup keeps only the full-width Contacts action. */
            @media (max-width: 650px) {
                .home-profile-modal .home-profile-links > .home-profile-link[href^="tel:"],
                .home-profile-modal .home-profile-links > .home-profile-link[href^="mailto:"] {
                    display: none !important;
                }
                .home-profile-modal .home-profile-links {
                    grid-template-columns: 1fr !important;
                    width: 100%;
                    max-width: 100%;
                    margin-inline: auto;
                }
                .home-profile-modal .home-profile-links > .home-profile-link.primary {
                    grid-column: 1 / -1 !important;
                    width: 100%;
                    min-height: 44px;
                    margin-inline: auto;
                }
            }
        </style>
    @endif
    @stack('head')
    <style>
        /* Mobile public navigation: nested folders stay in the vertical flow. */
        @media (max-width: 720px) {
            .public-header-nav .public-menu-dropdown {
                display: block;
                width: 100%;
                flex: 0 0 auto;
            }

            .public-header-nav .public-menu-dropdown-panel,
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel {
                position: static !important;
                inset: auto !important;
                top: auto !important;
                right: auto !important;
                bottom: auto !important;
                left: auto !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 6px 0 4px 12px !important;
                transform: none !important;
                background: transparent !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .public-header-nav .public-menu-dropdown.is-open > .public-menu-dropdown-panel {
                display: block;
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown {
                width: 100%;
            }

            .public-header-nav .public-menu-dropdown-panel > a,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
                width: 100%;
                min-height: 44px;
                height: auto;
                justify-content: space-between;
                box-sizing: border-box;
                padding: 10px 13px;
                border: 1px solid rgba(86,210,238,.075);
                border-left: 2px solid rgba(86,210,238,.24);
                border-radius: 0 10px 10px 0;
                background: rgba(67,209,240,.025);
                color: #a8c1c9 !important;
                font-size: 14px;
                line-height: 1.35;
                margin: 2px 0;
            }

            .public-header-nav .public-menu-dropdown-panel > a:hover,
            .public-header-nav .public-menu-dropdown-panel > a:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:hover,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] {
                background: rgba(67,209,240,.075);
                border-color: rgba(86,210,238,.14);
                border-left-color: rgba(86,210,238,.48);
                color: #effcff !important;
                outline: none;
            }

            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-panel {
                padding-left: 10px !important;
            }

            .public-header-nav .public-menu-dropdown > .public-menu-dropdown-toggle {
                position: relative;
            }

            .public-header-nav .public-menu-dropdown > .public-menu-dropdown-toggle i {
                flex: 0 0 auto;
            }

            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-chevron {
                flex: 0 0 auto;
                margin-left: 12px;
            }
        }
    </style>
</head>
<body>
    @if($useGlobalHeader) @include('partials.public-header', ['brand' => $publicBrand]) @endif
    @yield('content')
    @if($useGlobalFooter) @include('partials.public-footer', ['brand' => $publicBrand]) @endif
    @stack('scripts')
    <script>
        // Remove only the legacy escaped-newline artefact that can appear as a visible "\\n" text node.
        (() => {
            const cleanEscapedNewlines = () => {
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
                const nodes = [];
                while (walker.nextNode()) nodes.push(walker.currentNode);
                nodes.forEach(node => {
                    if (!node.nodeValue) return;
                    const cleaned = node.nodeValue.replaceAll('\\n', '').trim();
                    if (cleaned === '') {
                        node.remove();
                    } else if (cleaned !== node.nodeValue.trim()) {
                        node.nodeValue = cleaned;
                    }
                });
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', cleanEscapedNewlines, { once: true });
            } else {
                cleanEscapedNewlines();
            }
        })();
    </script>
</body>
</html>
