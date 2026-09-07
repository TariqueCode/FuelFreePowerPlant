@extends('layouts.public')

@section('title', ($item->meta_title ?: $item->title) . ' — ' . config('fuelfree.company.name'))

@php
    $safeHtml = static function (?string $html): string {
        $html = (string) $html;
        $html = preg_replace('/<\/?(?:script|style|object|embed|form)[^>]*>/i', '', $html) ?? $html;
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><a><span><div><table><thead><tbody><tfoot><tr><th><td><img><video><source><iframe><hr>');
        return preg_replace('/\s+(?:href|src)\s*=\s*(["\'])\s*(?:javascript|data|vbscript):[^"\']*\1/i', '', $html) ?? $html;
    };
@endphp

@section('content')
<style>
.gcms-page{width:min(1180px,calc(100% - 32px));margin:auto;padding:clamp(34px,6vw,72px) 0}.gcms-intro{margin-bottom:24px}.gcms-kicker{color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.gcms-title{margin:8px 0 12px;color:var(--public-text);font-size:clamp(32px,5vw,62px);line-height:1.05}.gcms-excerpt{max-width:850px;color:var(--public-muted);line-height:1.75}.gcms-content{border:1px solid color-mix(in srgb,var(--public-accent) 13%,transparent);border-radius:18px;background:var(--public-surface);box-shadow:0 18px 50px rgba(0,0,0,.1);padding:clamp(24px,5vw,54px);color:var(--public-text);line-height:1.8;overflow:hidden}.gcms-content h1,.gcms-content h2,.gcms-content h3,.gcms-content h4,.gcms-content h5,.gcms-content h6{color:var(--public-text);line-height:1.2;margin:1.2em 0 .55em}.gcms-content h1{font-size:clamp(30px,4vw,46px)}.gcms-content h2{font-size:clamp(25px,3.5vw,38px)}.gcms-content h3{font-size:clamp(21px,3vw,30px)}.gcms-content p{margin:.75em 0}.gcms-content a{color:var(--public-accent)}.gcms-content blockquote{margin:18px 0;padding:12px 18px;border-left:3px solid var(--public-accent);background:color-mix(in srgb,var(--public-accent) 6%,transparent)}.gcms-content pre{overflow:auto;padding:15px;border-radius:10px;background:#07151c}.gcms-content table{width:100%;border-collapse:collapse;margin:20px 0}.gcms-content th,.gcms-content td{border:1px solid color-mix(in srgb,var(--public-text) 18%,transparent);padding:9px;text-align:left}.gcms-content th{background:color-mix(in srgb,var(--public-accent) 8%,transparent)}.gcms-content img,.gcms-content video,.gcms-content iframe{max-width:100%;height:auto;border-radius:12px}.gcms-content iframe{width:100%;min-height:340px;border:0;background:#000}.gcms-content .ff-columns{display:grid;gap:16px;margin:20px 0}.gcms-content .ff-columns.cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.gcms-content .ff-columns.cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.gcms-content .ff-column{padding:18px;border:1px solid color-mix(in srgb,var(--public-accent) 13%,transparent);border-radius:12px}.gcms-content .ff-cta{display:inline-flex;align-items:center;justify-content:center;margin:8px 5px 8px 0;padding:11px 17px;border-radius:9px;background:var(--public-accent);color:#031018!important;text-decoration:none;font-weight:900}.gcms-content .ff-cta.outline{background:transparent;color:var(--public-accent)!important;border:1px solid var(--public-accent)}.gcms-content .ff-gallery{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:20px 0}.gcms-content .ff-gallery img{width:100%;aspect-ratio:16/10;object-fit:cover}.gcms-content hr{border:0;border-top:1px solid color-mix(in srgb,var(--public-accent) 35%,transparent);margin:28px 0}@media(max-width:700px){.gcms-page{width:calc(100% - 20px);padding:28px 0}.gcms-content{padding:24px 18px}.gcms-content .ff-columns,.gcms-content .ff-columns.cols-2,.gcms-content .ff-columns.cols-3{grid-template-columns:1fr}.gcms-content .ff-gallery{grid-template-columns:repeat(2,minmax(0,1fr))}.gcms-content iframe{min-height:240px}.gcms-content table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
<main class="gcms-page">
    <header class="gcms-intro">
        <div class="gcms-kicker">{{ config('fuelfree.company.name') }}</div>
        <h1 class="gcms-title">{{ $item->title }}</h1>
        @if($item->excerpt)<div class="gcms-excerpt">{{ $item->excerpt }}</div>@endif
    </header>
    <article class="gcms-content">
        {!! $safeHtml($item->content) !!}
    </article>
</main>
@endsection
