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
    @if($publicLogo)
        <link rel="icon" type="image/png" href="{{ asset('storage/'.ltrim($publicLogo,'/')) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/'.ltrim($publicLogo,'/')) }}">
    @endif
    <style>
        :root{--public-max:{{$theme['max_width']}};--public-gutter:16px;--public-bg:{{$theme['background']}};--public-surface:{{$theme['surface']}};--public-line:color-mix(in srgb, {{$theme['primary']}} 18%, transparent);--public-text:{{$theme['text']}};--public-muted:{{$theme['muted']}};--public-accent:{{$theme['primary']}};--public-radius:{{$theme['radius']}};--public-space-1:4px;--public-space-2:8px;--public-space-3:12px;--public-space-4:16px;--public-space-5:24px;--public-space-6:32px;--public-space-7:48px;--public-space-8:64px}
        .public-container{width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto}
        html{font-size:16px;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        body{margin:0;font-size:1rem;line-height:1.6;background:linear-gradient(180deg,{{$theme['background']}} 0%,{{$theme['surface']}} 52%,{{$theme['background']}} 100%);color:{{$theme['text']}};min-height:100vh;font-family:{{$theme['base_font']}};font-weight:400;letter-spacing:0}
        button,input,select,textarea{font:inherit}
        @media(max-width:600px){body{font-size:1rem}}
    </style>
    @stack('head')
</head>
<body>
    @if($useGlobalHeader) @include('partials.public-header', ['brand' => $publicBrand]) @endif
    @yield('content')
    @if($useGlobalFooter) @include('partials.public-footer', ['brand' => $publicBrand]) @endif
    @stack('scripts')
    <script>
        // Defensive cleanup for stray escaped newline text emitted by legacy/cached partials.
        (() => {
            const removeStrayEscapedNewlines = () => {
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
                const nodes = [];
                while (walker.nextNode()) nodes.push(walker.currentNode);
                nodes.forEach(node => {
                    if (node.nodeValue && node.nodeValue.trim() === '\\n') node.remove();
                });
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', removeStrayEscapedNewlines, { once: true });
            } else {
                removeStrayEscapedNewlines();
            }
        })();
    </script>
</body>
</html>
