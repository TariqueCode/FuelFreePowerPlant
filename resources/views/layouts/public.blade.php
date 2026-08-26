@php
    $publicBrand = $brand ?? [];
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand) ? $publicBrand->get('logo_path') : ($publicBrand['logo_path'] ?? null);
@endphp
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', $publicName)</title>
    @if($publicLogo)
        <link rel="icon" type="image/png" href="{{ asset('storage/'.ltrim($publicLogo,'/')) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/'.ltrim($publicLogo,'/')) }}">
    @endif
    @stack('head')
</head>
<body>
    @include('partials.public-header', ['brand' => $brand ?? []])
    @yield('content')
    @include('partials.public-footer', ['brand' => $brand ?? []])
    @stack('scripts')
</body>
</html>
