@php
    $publicBrand = $brand ?? null;
    if (!$publicBrand || (is_countable($publicBrand) && count($publicBrand) === 0)) {
        $publicBrand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    }
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
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
        :root{--public-max:1180px;--public-gutter:16px;--public-bg:#020a10;--public-surface:#071b25;--public-line:rgba(96,216,239,.16);--public-text:#edfaff;--public-muted:#8eaab4;--public-accent:#51d8f0;--public-radius:16px;--public-space-1:4px;--public-space-2:8px;--public-space-3:12px;--public-space-4:16px;--public-space-5:24px;--public-space-6:32px;--public-space-7:48px;--public-space-8:64px}
        .public-container{width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto}
        html{font-size:16px;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        body{margin:0;font-size:1rem;line-height:1.6;background:linear-gradient(180deg,#020a10 0%,#061721 52%,#020a10 100%);color:#effcff;min-height:100vh;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;font-weight:400;letter-spacing:0}
        button,input,select,textarea{font:inherit}
        @media(max-width:600px){body{font-size:1rem}}
    </style>
    @stack('head')
</head>
<body>
    @include('partials.public-header', ['brand' => $publicBrand])
    @yield('content')
    @include('partials.public-footer', ['brand' => $publicBrand])
    @stack('scripts')
</body>
</html>
