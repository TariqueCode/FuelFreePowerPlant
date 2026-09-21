@php
    $seo = config('fuelfree.seo', []);
    $siteName = $publicName ?? config('fuelfree.company.name', 'FuelFree PowerPlant Limited');
    $configuredDomain = trim((string) config('fuelfree.company.domain', request()->getHost()));
    $baseUrl = rtrim(
        preg_match('/^https?:\/\//i', $configuredDomain) ? $configuredDomain : 'https://' . $configuredDomain,
        '/'
    );
    $path = trim((string) request()->path(), '/');
    $canonicalUrl = $canonicalUrl ?? ($baseUrl . ($path === '' ? '/' : '/' . $path));
    $pageTitle = trim((string) ($metaTitle ?? $__env->yieldContent('title', $siteName)));
    $description = trim((string) ($metaDescription ?? $__env->yieldContent('meta_description', config('fuelfree.company.tagline', ''))));
    $robots = trim((string) ($metaRobots ?? $__env->yieldContent('meta_robots', 'index,follow')));
    $ogImage = trim((string) ($metaImage ?? $__env->yieldContent('og_image', '')));
    if ($ogImage !== '' && !str_starts_with($ogImage, 'http://') && !str_starts_with($ogImage, 'https://')) {
        $ogImage = $baseUrl . '/' . ltrim($ogImage, '/');
    }
    $logoUrl = trim((string) ($publicLogoUrl ?? config('fuelfree.company.logo_path', '')));
    if ($logoUrl !== '' && !str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
        $logoUrl = $baseUrl . '/' . ltrim($logoUrl, '/');
    }
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $baseUrl . '/#organization',
                'name' => $siteName,
                'url' => $baseUrl . '/',
                'description' => $description !== '' ? $description : config('fuelfree.company.tagline', ''),
                'logo' => $logoUrl !== '' ? ['@type' => 'ImageObject', 'url' => $logoUrl] : null,
                'telephone' => config('fuelfree.footer.phone'),
                'email' => config('fuelfree.footer.email'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => config('fuelfree.footer.address'),
                    'addressCountry' => 'BD',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $baseUrl . '/#website',
                'name' => $siteName,
                'url' => $baseUrl . '/',
                'publisher' => ['@id' => $baseUrl . '/#organization'],
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
            ],
            [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl . '#webpage',
                'url' => $canonicalUrl,
                'name' => $pageTitle,
                'description' => $description !== '' ? $description : config('fuelfree.company.tagline', ''),
                'isPartOf' => ['@id' => $baseUrl . '/#website'],
                'about' => ['@id' => $baseUrl . '/#organization'],
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
            ],
        ],
    ];
    if (isset($article)) {
        $articleImage = trim((string) ($article->image_path ?? ''));
        if ($articleImage !== '' && !str_starts_with($articleImage, 'http://') && !str_starts_with($articleImage, 'https://')) {
            $articleImage = $baseUrl . '/storage/' . ltrim($articleImage, '/');
        }
        $articleSchema = [
            '@type' => $article->type === 'announcement' ? 'Article' : 'NewsArticle',
            '@id' => $canonicalUrl . '#article',
            'headline' => (string) $article->title,
            'mainEntityOfPage' => ['@id' => $canonicalUrl],
            'publisher' => ['@id' => $baseUrl . '/#organization'],
        ];
        if ($article->published_at) $articleSchema['datePublished'] = $article->published_at->toAtomString();
        if ($article->updated_at) $articleSchema['dateModified'] = $article->updated_at->toAtomString();
        if ($articleImage !== '') $articleSchema['image'] = [$articleImage];
        $schema['@graph'][] = $articleSchema;
    }
@endphp
@if($description !== '')
<meta name="description" content="{{ $description }}">
@endif
<meta name="robots" content="{{ $robots }}">
<meta name="theme-color" content="#031018">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ isset($article) ? 'article' : 'website' }}">
<meta property="og:title" content="{{ $pageTitle }}">
@if($description !== '')
<meta property="og:description" content="{{ $description }}">
@endif
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
@if($ogImage !== '')
<meta property="og:image:alt" content="{{ $pageTitle }}">
@endif
@if($ogImage !== '')
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
@if($description !== '')
<meta name="twitter:description" content="{{ $description }}">
@endif
@if($ogImage !== '')
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $pageTitle }}">
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
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
