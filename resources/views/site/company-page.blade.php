@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
    $backRoute = $backRoute ?? route('home');
    $backLabel = $backLabel ?? 'Back to Home';
@endphp

@section('title', $item->title.' — '.$siteName)

@section('content')
<style>
.company-page-shell{width:min(1100px,calc(100% - 28px));margin:auto;padding:45px 0 55px}
.company-page-back{display:inline-flex;gap:8px;align-items:center;color:#8eb7c2;text-decoration:none;margin-bottom:20px;font-size:14px}
.company-page-hero{border:1px solid rgba(86,210,238,.15);border-radius:22px;background:linear-gradient(145deg,rgba(8,38,52,.95),rgba(3,21,30,.92));margin-bottom:25px}
.company-page-hero-content{padding:clamp(26px,5vw,48px);max-width:820px}
.company-page-hero h1{font-size:clamp(34px,6vw,60px);line-height:1.08;margin:0 0 14px;text-shadow:0 3px 18px rgba(0,0,0,.35)}
.company-page-hero p{color:#c2d8de;font-size:18px;line-height:1.7;margin:0;max-width:760px}
.company-page-content{border:1px solid rgba(86,210,238,.15);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.82),rgba(3,21,30,.8));padding:clamp(20px,4vw,36px);font-size:17px;line-height:1.85;color:#c7dce2}
.company-page-content img,.company-page-content video{max-width:100%;height:auto;border-radius:14px}
.company-page-content h1,.company-page-content h2,.company-page-content h3{color:#effcff}
.company-page-content h1{font-size:clamp(28px,4vw,44px);line-height:1.2}
.company-page-content h2,.company-page-content h3{margin-top:1.4em}
.company-page-content table{width:100%;border-collapse:collapse;margin:22px 0}
.company-page-content th,.company-page-content td{border:1px solid rgba(86,210,238,.15);padding:10px;text-align:left}
.company-page-content th{background:rgba(67,209,240,.08);color:#effcff}
.company-page-content blockquote{margin:20px 0;padding:14px 18px;border-left:4px solid #43d1f0;background:rgba(67,209,240,.06);border-radius:0 10px 10px 0}
.company-page-content pre{padding:16px;border-radius:12px;background:#031017;overflow:auto;color:#c8f3fa}
.company-page-content hr{border:0;border-top:1px solid rgba(86,210,238,.15);margin:28px 0}
.company-page-content .editor-callout{padding:16px 18px;border:1px solid rgba(67,209,240,.22);border-radius:12px;background:rgba(67,209,240,.06);margin:18px 0}
.company-page-content .editor-button{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:9px;background:#29aaca;color:#fff!important;text-decoration:none!important;font-weight:700;margin:6px 4px}
.company-page-content iframe{width:100%;min-height:360px;border:0;border-radius:14px;margin:16px 0}.content-columns{display:grid;gap:18px;margin:18px 0}.content-columns.cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.content-columns.cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.content-column{min-width:0;padding:18px;border:1px solid rgba(86,210,238,.15);border-radius:12px;background:rgba(67,209,240,.025)}.content-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;margin:10px 4px 10px 0;padding:11px 17px;border-radius:9px;background:#29aaca;color:#fff!important;text-decoration:none!important;font-weight:700;line-height:1.2}.content-cta.cta-outline{background:transparent;color:#29aaca!important;border:1px solid #29aaca}.media-gallery{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:14px 0}.media-gallery img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:10px}.align-left{display:block;margin-left:0;margin-right:auto}.align-center{display:block;margin-left:auto;margin-right:auto}.align-right{display:block;margin-left:auto;margin-right:0}.rich table,.content table,.company-page-content table{width:100%;border-collapse:collapse}.rich th,.rich td,.content th,.content td,.company-page-content th,.company-page-content td{border:1px solid rgba(86,210,238,.15);padding:10px;text-align:left}.rich blockquote,.content blockquote,.company-page-content blockquote{margin:20px 0;padding:14px 18px;border-left:4px solid #43d1f0;background:rgba(67,209,240,.06);border-radius:0 10px 10px 0}.rich pre,.content pre,.company-page-content pre{padding:16px;border-radius:12px;background:#031017;overflow:auto;color:#c8f3fa}.rich hr,.content hr,.company-page-content hr{border:0;border-top:1px solid rgba(86,210,238,.15);margin:28px 0}@media(max-width:700px){.content-columns.cols-2,.content-columns.cols-3{grid-template-columns:1fr}.media-gallery{grid-template-columns:1fr 1fr}.rich table,.content table,.company-page-content table{display:block;overflow-x:auto;-webkit-overflow-scrolling:touch}.content-column{padding:14px}}
@media(max-width:600px){.company-page-shell{padding-top:30px}.company-page-hero{border-radius:17px}.company-page-hero-content{padding:24px 20px}.company-page-hero h1{font-size:36px}.company-page-hero p,.company-page-content{font-size:16px;line-height:1.8}.company-page-content table{display:block;overflow-x:auto;white-space:nowrap}.company-page-content iframe{min-height:220px}}
.page-builder-content{display:grid;gap:20px;margin-bottom:24px}.pb-hero,.pb-image-text{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(260px,.85fr);gap:24px;align-items:center;padding:24px;border:1px solid rgba(86,210,238,.15);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.82),rgba(3,21,30,.8))}.pb-hero h2,.pb-image-text h2,.pb-text h2{color:#effcff;margin-top:0}.pb-hero img,.pb-image-text img,.pb-image img{width:100%;max-width:100%;height:auto;border-radius:14px;object-fit:cover}.pb-image{margin:0;padding:18px;border:1px solid rgba(86,210,238,.15);border-radius:18px;background:rgba(8,38,52,.6)}.pb-image figcaption{margin-top:9px;color:#9bbbc4;font-size:13px}.pb-video iframe{width:100%;min-height:420px;border:0;border-radius:16px}@media(max-width:700px){.pb-hero,.pb-image-text{grid-template-columns:1fr;padding:18px}.pb-video iframe{min-height:230px}}</style>

<main class="company-page-shell">
    <a class="company-page-back" href="{{ $backRoute }}"><i class="fa-solid fa-arrow-left"></i> {{ $backLabel }}</a>
    <section class="company-page-hero">
        <div class="company-page-hero-content">
            <h1>{{ $item->title }}</h1>
            @if($item->excerpt)<p>{{ $item->excerpt }}</p>@endif
        </div>
    </section>
    @if(!empty($item->builder_blocks))
        <section class="page-builder-content" aria-label="Page sections">
            @foreach($item->builder_blocks as $block)
                @if(($block['visible'] ?? true) === false) @continue @endif
                @php($blockType = $block['type'] ?? 'text')
                @if($blockType === 'hero')
                    <section class="pb-hero"><div><h2>{{ $block['title'] ?? '' }}</h2><div>{!! $block['content'] ?? '' !!}</div></div>@if(!empty($block['image']))<img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? '' }}">@endif</section>
                @elseif($blockType === 'image')
                    @if(!empty($block['image']))<figure class="pb-image"><img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? '' }}">@if(!empty($block['title']))<figcaption>{{ $block['title'] }}</figcaption>@endif</figure>@endif
                @elseif($blockType === 'image_text')
                    <section class="pb-image-text">@if(!empty($block['image']))<img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? '' }}">@endif<div><h2>{{ $block['title'] ?? '' }}</h2><div>{!! $block['content'] ?? '' !!}</div></div></section>
                @elseif($blockType === 'video')
                    @if(!empty($block['url']))<div class="pb-video"><iframe src="{{ $block['url'] }}" title="{{ $block['title'] ?? 'Video' }}" loading="lazy" allowfullscreen></iframe></div>@endif
                @elseif($blockType === 'cta' || $blockType === 'button')
                    @if(!empty($block['url']))<a class="content-cta" href="{{ $block['url'] }}">{{ $block['title'] ?: 'Learn more' }}</a>@endif
                @elseif($blockType === 'divider')
                    <hr>
                @elseif($blockType === 'html')
                    {!! $block['content'] ?? '' !!}
                @elseif($blockType === 'gallery')
                    @if(!empty($block['image']))<div class="media-gallery"><img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? '' }}"></div>@endif
                @else
                    <section class="pb-text">@if(!empty($block['title']))<h2>{{ $block['title'] }}</h2>@endif{!! $block['content'] ?? '' !!}</section>
                @endif
            @endforeach
        </section>
    @endif
    <article class="company-page-content">{!! $item->content !!}</article>
</main>
@endsection
