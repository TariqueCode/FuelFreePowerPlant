@extends('layouts.public')

@section('title', ($item->meta_title ?: $item->title) . ' — ' . config('fuelfree.company.name'))

@php
    $blocks = is_array($item->builder_blocks) ? $item->builder_blocks : [];
    $safeHtml = static function (?string $html): string {
        $html = (string) $html;
        if ($html === '') return '';
        $allowed = '<p><br><strong><b><em><i><u><s><h2><h3><h4><ul><ol><li><blockquote><a><span> ';
        $html = strip_tags($html, $allowed);
        return preg_replace_callback('/<a\s+([^>]*?)>/i', static function ($match) {
            $attrs = $match[1];
            if (preg_match('/href\s*=\s*["\']\s*(javascript:|data:|vbscript:)/i', $attrs)) {
                $attrs = preg_replace('/\s*href\s*=\s*["\'][^"\']*["\']/i', '', $attrs);
            }
            return '<a '.$attrs.' rel="noopener noreferrer">';
        }, $html) ?? '';
    };
    $safeUrl = static function (?string $url): string {
        $url = trim((string) $url);
        if ($url === '' || preg_match('/^(javascript|data|vbscript):/i', $url)) return '#';
        return $url;
    };
@endphp

@section('content')
<style>
.pb-public{--pb-max:var(--public-max,1280px);--pb-gutter:var(--public-gutter,16px);width:min(var(--pb-max),calc(100% - (var(--pb-gutter) * 2)));margin:0 auto;padding:clamp(28px,5vw,72px) 0 clamp(46px,7vw,96px)}
.pb-public-intro{position:relative;overflow:hidden;margin-bottom:18px;padding:clamp(26px,5vw,58px);border:1px solid color-mix(in srgb,var(--public-accent) 17%,transparent);border-radius:var(--public-radius,16px);background:linear-gradient(135deg,color-mix(in srgb,var(--public-surface) 94%,transparent),color-mix(in srgb,var(--public-bg) 92%,transparent));box-shadow:0 22px 60px rgba(0,0,0,.16)}
.pb-public-intro:before{content:"";position:absolute;inset:-30% auto auto 55%;width:420px;height:260px;border-radius:50%;background:radial-gradient(circle,color-mix(in srgb,var(--public-accent) 13%,transparent),transparent 70%);pointer-events:none}.pb-public-kicker{position:relative;color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.pb-public-title{position:relative;max-width:980px;margin:8px 0 10px;color:var(--public-text);font-size:clamp(30px,6vw,68px);line-height:1.02;letter-spacing:-1.5px}.pb-public-excerpt{position:relative;max-width:780px;color:var(--public-muted);font-size:clamp(14px,2vw,18px);line-height:1.7}.pb-public-sections{display:grid;gap:18px}.pb-block{overflow:hidden;border:1px solid color-mix(in srgb,var(--public-accent) 13%,transparent);border-radius:var(--public-radius,16px);background:linear-gradient(145deg,color-mix(in srgb,var(--public-surface) 96%,transparent),color-mix(in srgb,var(--public-bg) 96%,transparent));box-shadow:0 18px 50px rgba(0,0,0,.1)}.pb-block-inner{max-width:900px;padding:clamp(24px,5vw,52px)}.pb-block.tone-accent{border-color:color-mix(in srgb,var(--public-accent) 27%,transparent);background:linear-gradient(145deg,color-mix(in srgb,var(--public-accent) 10%,var(--public-surface)),color-mix(in srgb,var(--public-surface) 95%,transparent))}.pb-block.tone-light{background:linear-gradient(145deg,#102a34,#0a202a)}.pb-block.center{text-align:center}.pb-block.right{text-align:right}.pb-block-kicker{color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.15em;text-transform:uppercase}.pb-block-title{margin:7px 0 10px;color:var(--public-text);font-size:clamp(23px,4vw,42px);line-height:1.08;letter-spacing:-.6px}.pb-copy{color:var(--public-muted);font-size:clamp(14px,1.8vw,17px);line-height:1.8}.pb-copy p{margin:0 0 1em}.pb-copy p:last-child{margin-bottom:0}.pb-copy h2,.pb-copy h3,.pb-copy h4{color:var(--public-text);line-height:1.2}.pb-copy a{color:var(--public-accent)}.pb-copy blockquote{margin:18px 0;padding:14px 18px;border-left:3px solid var(--public-accent);background:rgba(255,255,255,.025);color:#b9d0d7}.pb-button{display:inline-flex;align-items:center;justify-content:center;min-height:46px;margin-top:18px;padding:0 18px;border-radius:11px;background:linear-gradient(135deg,var(--public-accent),color-mix(in srgb,var(--public-accent) 70%,#087b93));color:#031018!important;text-decoration:none!important;font-weight:900;font-size:12px;box-shadow:0 10px 28px color-mix(in srgb,var(--public-accent) 14%,transparent)}
.pb-hero{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(280px,.92fr);align-items:stretch}.pb-hero .pb-block-inner{display:flex;flex-direction:column;justify-content:center}.pb-media{min-height:320px;background:rgba(255,255,255,.025)}.pb-media img{display:block;width:100%;height:100%;min-height:320px;object-fit:cover}.pb-split{display:grid;grid-template-columns:1fr 1fr;align-items:stretch}.pb-split.image-right .pb-block-inner{order:1}.pb-split.image-right .pb-media{order:2}.pb-split .pb-media{min-height:300px}.pb-image img{display:block;width:100%;max-height:620px;object-fit:cover}.pb-image-caption{padding:14px 18px;color:var(--public-muted);font-size:12px}.pb-cards-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:0 clamp(18px,4vw,38px) clamp(18px,4vw,38px)}.pb-card-item{padding:20px;border:1px solid color-mix(in srgb,var(--public-accent) 11%,transparent);border-radius:13px;background:rgba(255,255,255,.018)}.pb-card-item h3{margin:0 0 7px;color:var(--public-text);font-size:17px}.pb-card-item p{margin:0;color:var(--public-muted);font-size:13px;line-height:1.65}.pb-card-item img{display:block;width:100%;height:150px;margin:-20px -20px 16px;width:calc(100% + 40px);object-fit:cover;border-radius:12px 12px 0 0}.pb-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;padding:0 clamp(18px,4vw,38px) clamp(18px,4vw,38px)}.pb-stat{text-align:center;padding:18px 10px;border:1px solid color-mix(in srgb,var(--public-accent) 12%,transparent);border-radius:12px;background:rgba(255,255,255,.018)}.pb-stat strong{display:block;color:var(--public-text);font-size:clamp(25px,4vw,40px);line-height:1}.pb-stat span{display:block;margin-top:6px;color:var(--public-muted);font-size:11px}.pb-cta{text-align:center;padding:clamp(28px,6vw,58px)}.pb-video{aspect-ratio:16/9;background:#020c12}.pb-video iframe{display:block;width:100%;height:100%;border:0}.pb-video-placeholder{display:grid;place-items:center;height:100%;color:var(--public-muted);font-size:15px}.pb-divider{height:1px;margin:10px 8%;background:linear-gradient(90deg,transparent,color-mix(in srgb,var(--public-accent) 35%,transparent),transparent)}
@media(max-width:850px){.pb-hero,.pb-split{grid-template-columns:1fr}.pb-split.image-right .pb-block-inner,.pb-split.image-right .pb-media{order:initial}.pb-media,.pb-split .pb-media{min-height:230px}.pb-media img{min-height:230px}.pb-cards-grid{grid-template-columns:1fr 1fr}.pb-stats{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.pb-public{width:min(100% - 20px,1280px);padding-top:20px}.pb-public-intro{padding:24px 20px}.pb-public-title{font-size:34px}.pb-block-inner{padding:24px 20px}.pb-cards-grid{grid-template-columns:1fr;padding:0 14px 14px}.pb-stats{grid-template-columns:1fr 1fr;padding:0 14px 14px}.pb-card-item{padding:17px}.pb-card-item img{margin:-17px -17px 14px;width:calc(100% + 34px)}}
</style>

<main class="pb-public">
    <header class="pb-public-intro">
        <div class="pb-public-kicker">{{ config('fuelfree.company.name') }}</div>
        <h1 class="pb-public-title">{{ $item->title }}</h1>
        @if($item->excerpt)<div class="pb-public-excerpt">{{ $item->excerpt }}</div>@endif
    </header>

    <div class="pb-public-sections">
        @foreach($blocks as $block)
            @php
                if (!is_array($block) || (($block['visible'] ?? true) === false)) continue;
                $type = $block['type'] ?? 'rich_text';
                $tone = in_array($block['tone'] ?? 'dark', ['dark','accent','light'], true) ? $block['tone'] : 'dark';
                $align = in_array($block['align'] ?? 'left', ['left','center','right'], true) ? $block['align'] : 'left';
                $title = $block['title'] ?? '';
                $content = $safeHtml($block['content'] ?? '');
            @endphp

            @if($type === 'divider')
                <div class="pb-divider" aria-hidden="true"></div>
            @elseif($type === 'hero')
                <section class="pb-block tone-{{ $tone }} {{ $align }}"><div class="pb-hero"><div class="pb-block-inner"><div class="pb-block-kicker">{{ $block['eyebrow'] ?? '' }}</div>@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div>@if(!empty($block['button_text']))<a class="pb-button" href="{{ $safeUrl($block['button_url'] ?? '#') }}">{{ $block['button_text'] }}</a>@endif</div><div class="pb-media">@if(!empty($block['image']))<img src="{{ $safeUrl($block['image']) }}" alt="{{ $block['image_alt'] ?? $title }}" loading="lazy">@endif</div></div></section>
            @elseif($type === 'rich_text')
                <section class="pb-block tone-{{ $tone }}"><div class="pb-block-inner {{ $align }}">@if(!empty($block['eyebrow']))<div class="pb-block-kicker">{{ $block['eyebrow'] }}</div>@endif@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div></div></section>
            @elseif($type === 'image')
                <section class="pb-block tone-{{ $tone }}"><div class="pb-image">@if(!empty($block['image']))<img src="{{ $safeUrl($block['image']) }}" alt="{{ $block['image_alt'] ?? $title }}" loading="lazy">@endif</div>@if($title || $content)<div class="pb-block-inner {{ $align }}">@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div></div>@endif</section>
            @elseif($type === 'split')
                <section class="pb-block tone-{{ $tone }}"><div class="pb-split {{ ($block['layout'] ?? 'image-left') === 'image-right' ? 'image-right' : '' }}"><div class="pb-media">@if(!empty($block['image']))<img src="{{ $safeUrl($block['image']) }}" alt="{{ $block['image_alt'] ?? $title }}" loading="lazy">@endif</div><div class="pb-block-inner {{ $align }}">@if(!empty($block['eyebrow']))<div class="pb-block-kicker">{{ $block['eyebrow'] }}</div>@endif@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div>@if(!empty($block['button_text']))<a class="pb-button" href="{{ $safeUrl($block['button_url'] ?? '#') }}">{{ $block['button_text'] }}</a>@endif</div></div></section>
            @elseif($type === 'cards')
                @php $columns = min(4, max(2, (int) ($block['columns'] ?? 3))); $items = is_array($block['items'] ?? null) ? $block['items'] : []; @endphp
                <section class="pb-block tone-{{ $tone }}"><div class="pb-block-inner {{ $align }}">@if(!empty($block['eyebrow']))<div class="pb-block-kicker">{{ $block['eyebrow'] }}</div>@endif@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div></div><div class="pb-cards-grid" style="grid-template-columns:repeat({{ $columns }},minmax(0,1fr))">@foreach($items as $card)<article class="pb-card-item">@if(!empty($card['image']))<img src="{{ $safeUrl($card['image']) }}" alt="{{ $card['title'] ?? '' }}" loading="lazy">@endif<h3>{{ $card['title'] ?? 'Feature' }}</h3><p>{{ $card['content'] ?? '' }}</p></article>@endforeach</div></section>
            @elseif($type === 'stats')
                @php $items = is_array($block['items'] ?? null) ? $block['items'] : []; @endphp
                <section class="pb-block tone-{{ $tone }}"><div class="pb-block-inner {{ $align }}">@if(!empty($block['eyebrow']))<div class="pb-block-kicker">{{ $block['eyebrow'] }}</div>@endif@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div></div><div class="pb-stats">@foreach($items as $stat)<div class="pb-stat"><strong>{{ $stat['value'] ?? '—' }}</strong><span>{{ $stat['label'] ?? '' }}</span></div>@endforeach</div></section>
            @elseif($type === 'cta')
                <section class="pb-block tone-{{ $tone }}"><div class="pb-cta {{ $align }}">@if(!empty($block['eyebrow']))<div class="pb-block-kicker">{{ $block['eyebrow'] }}</div>@endif@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div>@if(!empty($block['button_text']))<a class="pb-button" href="{{ $safeUrl($block['button_url'] ?? '#') }}">{{ $block['button_text'] }}</a>@endif</div></section>
            @elseif($type === 'video')
                <section class="pb-block tone-{{ $tone }}"><div class="pb-video">@if(!empty($block['url']))<iframe src="{{ $safeUrl($block['url']) }}" title="{{ $title ?: 'Page video' }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>@else<div class="pb-video-placeholder"><i class="fa-solid fa-circle-play" style="margin-right:8px"></i>Video URL not configured</div>@endif</div>@if($title || $content)<div class="pb-block-inner {{ $align }}">@if($title)<h2 class="pb-block-title">{{ $title }}</h2>@endif<div class="pb-copy">{!! $content !!}</div></div>@endif</section>
            @endif
        @endforeach
    </div>
</main>
@endsection
