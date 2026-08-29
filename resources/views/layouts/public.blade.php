@php
    $globalLayout = $globalLayout ?? app(\App\Services\GlobalLayoutService::class)->all();
    $publicBrand = $brand ?? null;
    $theme = array_merge(['primary'=>'#55cce7','secondary'=>'#0f2430','accent'=>'#9de8f7','surface'=>'#07131a','text'=>'#eaf7fb','muted'=>'#8ea8b2','radius'=>'12','font_body'=>'Inter, sans-serif','font_heading'=>'Inter, sans-serif','base_size'=>'16','line_height'=>'1.6','space_section'=>'64','space_content'=>'24'], collect($globalLayout ?? [])->filter(fn($v,$k)=>str_starts_with($k,'theme.'))->mapWithKeys(fn($v,$k)=>[substr($k,6)=>$v])->all());
    if (!$publicBrand || (is_countable($publicBrand) && count($publicBrand) === 0)) {
        $publicBrand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    }
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
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
         :root{--public-max:1180px;--public-gutter:16px;--public-bg:#020a10;--public-surface:{{ $theme['surface'] }};--public-line:rgba(96,216,239,.16);--public-text:{{ $theme['text'] }};--public-muted:{{ $theme['muted'] }};--public-accent:{{ $theme['primary'] }};--public-radius:{{ (int)$theme['radius'] }}px;--public-base-size:{{ (int)$theme['base_size'] }}px;--public-line-height:{{ $theme['line_height'] }};--public-space-section:{{ (int)$theme['space_section'] }}px;--public-space-content:{{ (int)$theme['space_content'] }}px;--public-card-padding:{{ (int)$theme['card_padding'] }}px;--public-button-radius:{{ (int)$theme['button_radius'] }}px;--public-button-height:{{ (int)$theme['button_height'] }}px;--public-input-radius:{{ (int)$theme['input_radius'] }}px;--public-font-body:{{ $theme['font_body'] }};--public-font-heading:{{ $theme['font_heading'] }};--public-space-1:4px;--public-space-2:8px;--public-space-3:12px;--public-space-4:16px;--public-space-5:24px;--public-space-6:32px;--public-space-7:48px;--public-space-8:64px}
        .public-container{width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto}
        html{font-size:16px;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        body{margin:0;font-size:var(--public-base-size);line-height:var(--public-line-height);background:linear-gradient(180deg,#020a10 0%,#061721 52%,#020a10 100%);color:var(--public-text);min-height:100vh;font-family:var(--public-font-body);font-weight:400;letter-spacing:0}
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
