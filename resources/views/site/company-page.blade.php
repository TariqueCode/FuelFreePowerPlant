@extends('layouts.public')

@section('title', ($item->meta_title ?: $item->title) . ' — ' . config('fuelfree.company.name'))

@php
    $blocks = is_array($item->builder_blocks) ? $item->builder_blocks : [];
    $safeHtml = static fn (?string $html): string => strip_tags((string) $html, '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><a><span>');
    $safeUrl = static function (?string $url): string { $url=trim((string)$url); return ($url==='' || preg_match('/^(javascript|data|vbscript):/i',$url)) ? '#' : $url; };
@endphp

@section('content')
<style>
.pb-page{width:min(1280px,calc(100% - 32px));margin:auto;padding:56px 0}.pb-intro,.pb-block{border:1px solid color-mix(in srgb,var(--public-accent) 14%,transparent);border-radius:16px;background:var(--public-surface);box-shadow:0 18px 50px rgba(0,0,0,.1)}.pb-intro{padding:clamp(28px,5vw,58px);margin-bottom:18px}.pb-kicker{color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.pb-title{margin:8px 0;color:var(--public-text);font-size:clamp(30px,6vw,68px);line-height:1.02}.pb-excerpt,.pb-copy{color:var(--public-muted);line-height:1.75}.pb-sections{display:grid;gap:18px}.pb-inner{padding:clamp(24px,5vw,52px)}.pb-block h2{margin:7px 0 12px;color:var(--public-text);font-size:clamp(23px,4vw,42px)}.pb-button{display:inline-flex;margin-top:18px;padding:13px 18px;border-radius:10px;background:var(--public-accent);color:#031018!important;text-decoration:none!important;font-weight:900}.pb-hero,.pb-split{display:grid;grid-template-columns:1fr 1fr}.pb-media{min-height:300px}.pb-media img,.pb-image img{display:block;width:100%;height:100%;min-height:300px;object-fit:cover}.pb-split.image-right .pb-inner{order:2}.pb-cards,.pb-stats{display:grid;gap:12px;padding:0 38px 38px}.pb-card,.pb-stat{padding:20px;border:1px solid color-mix(in srgb,var(--public-accent) 12%,transparent);border-radius:12px}.pb-card h3{margin:0 0 8px;color:var(--public-text)}.pb-card p,.pb-stat span{margin:0;color:var(--public-muted)}.pb-stat{text-align:center}.pb-stat strong{display:block;color:var(--public-text);font-size:32px}.pb-cta{text-align:center;padding:52px}.pb-video{aspect-ratio:16/9}.pb-video iframe{width:100%;height:100%;border:0}.pb-divider{height:1px;margin:10px 8%;background:var(--public-accent)}@media(max-width:850px){.pb-hero,.pb-split{grid-template-columns:1fr}.pb-split.image-right .pb-inner{order:initial}.pb-cards{grid-template-columns:1fr 1fr!important}.pb-stats{grid-template-columns:1fr 1fr}}@media(max-width:560px){.pb-page{width:calc(100% - 20px);padding:24px 0}.pb-cards,.pb-stats{grid-template-columns:1fr!important;padding:0 14px 14px}.pb-inner{padding:24px 20px}}
</style>
<main class="pb-page">
<header class="pb-intro"><div class="pb-kicker">{{ config('fuelfree.company.name') }}</div><h1 class="pb-title">{{ $item->title }}</h1>@if($item->excerpt)<div class="pb-excerpt">{{ $item->excerpt }}</div>@endif</header>
<div class="pb-sections">
@foreach($blocks as $block)
@if(!is_array($block) || (($block['visible'] ?? true) === false)) @continue @endif
@php $type=$block['type']??'rich_text'; $title=$block['title']??''; $content=$safeHtml($block['content']??''); $image=$safeUrl($block['image']??'#'); @endphp
@if($type==='divider')<div class="pb-divider"></div>
@elseif($type==='hero')<section class="pb-block"><div class="pb-hero"><div class="pb-inner"><div class="pb-kicker">{{ $block['eyebrow']??'' }}</div><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div>@if(!empty($block['button_text']))<a class="pb-button" href="{{ $safeUrl($block['button_url']??'#') }}">{{ $block['button_text'] }}</a>@endif</div><div class="pb-media">@if($image!=='#')<img src="{{ $image }}" alt="{{ $block['image_alt']??$title }}" loading="lazy">@endif</div></div></section>
@elseif($type==='rich_text')<section class="pb-block"><div class="pb-inner"><div class="pb-kicker">{{ $block['eyebrow']??'' }}</div><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div></div></section>
@elseif($type==='image')<section class="pb-block"><div class="pb-image">@if($image!=='#')<img src="{{ $image }}" alt="{{ $block['image_alt']??$title }}" loading="lazy">@endif</div><div class="pb-inner"><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div></div></section>
@elseif($type==='split')<section class="pb-block"><div class="pb-split {{ ($block['layout']??'image-left')==='image-right'?'image-right':'' }}"><div class="pb-media">@if($image!=='#')<img src="{{ $image }}" alt="{{ $block['image_alt']??$title }}" loading="lazy">@endif</div><div class="pb-inner"><div class="pb-kicker">{{ $block['eyebrow']??'' }}</div><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div></div></div></section>
@elseif($type==='cards')@php $items=is_array($block['items']??null)?$block['items']:[]; $cols=min(4,max(2,(int)($block['columns']??3))); @endphp<section class="pb-block"><div class="pb-inner"><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div></div><div class="pb-cards" style="grid-template-columns:repeat({{ $cols }},1fr)">@foreach($items as $card)<article class="pb-card"><h3>{{ $card['title']??'Feature' }}</h3><p>{{ $card['content']??'' }}</p></article>@endforeach</div></section>
@elseif($type==='stats')@php $items=is_array($block['items']??null)?$block['items']:[]; @endphp<section class="pb-block"><div class="pb-inner"><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div></div><div class="pb-stats">@foreach($items as $stat)<div class="pb-stat"><strong>{{ $stat['value']??'—' }}</strong><span>{{ $stat['label']??'' }}</span></div>@endforeach</div></section>
@elseif($type==='cta')<section class="pb-block"><div class="pb-cta"><div class="pb-kicker">{{ $block['eyebrow']??'' }}</div><h2>{{ $title }}</h2><div class="pb-copy">{!! $content !!}</div>@if(!empty($block['button_text']))<a class="pb-button" href="{{ $safeUrl($block['button_url']??'#') }}">{{ $block['button_text'] }}</a>@endif</div></section>
@elseif($type==='video')<section class="pb-block"><div class="pb-video">@if(!empty($block['url']))<iframe src="{{ $safeUrl($block['url']) }}" title="{{ $title?:'Page video' }}" loading="lazy" allowfullscreen></iframe>@else<div class="pb-cta">Video URL not configured</div>@endif</div></section>
@endif
@endforeach
</div></main>
@endsection
