@php
    $publicBrand = $brand ?? null;
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
    @stack('head')
</head>
<body>
    @include('partials.public-header', ['brand' => $publicBrand])
    @yield('content')
    @include('partials.public-footer', ['brand' => $publicBrand])
    @stack('scripts')
</body>
</html>
