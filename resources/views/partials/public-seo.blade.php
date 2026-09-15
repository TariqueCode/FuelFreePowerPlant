@php
    $seo = config('fuelfree.seo', []);
    $siteName = config('fuelfree.company.name', 'FuelFree PowerPlant Limited');
    $configuredDomain = trim((string) config('fuelfree.company.domain', request()->getHost()));
    $baseUrl = rtrim(
        preg_match('/^https?:\/\//i', $configuredDomain) ? $configuredDomain : 'https://' . $configuredDomain,
        '/'
    );
    $path = trim((string) request()->path(), '/');
    $canonicalUrl = $baseUrl . ($path === '' ? '/' : '/' . $path);
    $pageTitle = trim((string) $__env->yieldContent('title', $siteName));
    $description = trim((string) $__env->yieldContent('meta_description', config('fuelfree.company.tagline', '')));
    $robots = trim((string) $__env->yieldContent('meta_robots', 'index,follow'));
    $ogImage = trim((string) $__env->yieldContent('og_image', config('fuelfree.company.logo_path', '')));
    if ($ogImage !== '' && !str_starts_with($ogImage, 'http://') && !str_starts_with($ogImage, 'https://')) {
        $ogImage = $baseUrl . '/' . ltrim($ogImage, '/');
    }
@endphp
@if($description !== '')
<meta name="description" content="{{ $description }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }}">
@if($description !== '')
<meta property="og:description" content="{{ $description }}">
@endif
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if($ogImage !== '')
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
@if($description !== '')
<meta name="twitter:description" content="{{ $description }}">
@endif
@if($ogImage !== '')
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if(!empty($seo['google_verification']))
<meta name="google-site-verification" content="{{ $seo['google_verification'] }}">
@endif
@if(!empty($seo['bing_verification']))
<meta name="msvalidate.01" content="{{ $seo['bing_verification'] }}">
@endif
@if(!empty($seo['meta_verification']))
<meta name="facebook-domain-verification" content="{{ $seo['meta_verification'] }}">
@endif
@if(!empty($seo['ga4_measurement_id']) && preg_match('/^G-[A-Z0-9]+$/i', (string) $seo['ga4_measurement_id']))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($seo['ga4_measurement_id']) }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', @json($seo['ga4_measurement_id']));
</script>
@endif
