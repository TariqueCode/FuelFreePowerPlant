@extends('layouts.public')

@section('title', ($item->meta_title ?: $item->title) . ' — ' . config('fuelfree.company.name'))

@php
    $blocks = is_array($item->builder_blocks) ? $item->builder_blocks : [];
    $safeHtml = static function (?string $html): string {
        $html = (string) $html;
        if ($html === '') return '';
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><h2><h3><h4><ul><ol><li><blockquote><a><span>');
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
    $renderBlock = static function (array $block) use ($safeHtml, $safeUrl): string {
        if (($block['visible'] ?? true) === false) return '';
        $type = $block['type'] ?? 'rich_text';
        $tone = in_array($block['tone'] ?? 'dark', ['dark', 'accent', 'light'], true) ? $block['tone'] : 'dark';
        $align = in_array($block['align'] ?? 'left', ['left', 'center', 'right'], true) ? $block['align'] : 'left';
        $title = e($block['title'] ?? '');
        $eyebrow = e($block['eyebrow'] ?? '');
        $content = $safeHtml($block['content'] ?? '');
        $image = $safeUrl($block['image'] ?? '');
        $alt = e($block['image_alt'] ?? ($block['title'] ?? ''));
        $buttonText = e($block['button_text'] ?? '');
        $buttonUrl = $safeUrl($block['button_url'] ?? '#');
        $heading = $title !== '' ? '<h2 class="pb-block-title">'.$title.'</h2>' : '';
        $kicker = $eyebrow !== '' ? '<div class="pb-block-kicker">'.$eyebrow.'</div>' : '';
        $copy = $content !== '' ? '<div class="pb-copy">'.$content.'</div>' : '';
        $button = $buttonText !== '' ? '<a class="pb-button" href="'.e($buttonUrl).'">'.$buttonText.'</a>' : '';
        $shell = 'pb-block tone-'.$tone;
        if ($type === 'divider') return '<div class="pb-divider" aria-hidden="true"></div>';
        if ($type === 'hero') {
            $media = $image !== '' ? '<img src="'.e($image).'" alt="'.$alt.'" loading="lazy">' : '';
            return '<section class="'.$shell.' '.$align.'"><div class="pb-hero"><div class="pb-block-inner">'.$kicker.$heading.$copy.$button.'</div><div class="pb-media">'.$media.'</div></div></section>';
        }
        if ($type === 'rich_text') return '<section class="'.$shell.'"><div class="pb-block-inner '.$align.'">'.$kicker.$heading.$copy.'</div></section>';
        if ($type === 'image') {
            $media = $image !== '' ? '<img src="'.e($image).'" alt="'.$alt.'" loading="lazy">' : '';
            return '<section class="'.$shell.'"><div class="pb-image">'.$media.'</div>'.(($heading !== '' || $copy !== '') ? '<div class="pb-block-inner '.$align.'">'.$heading.$copy.'</div>' : '').'</section>';
        }
        if ($type === 'split') {
            $media = $image !== '' ? '<img src="'.e($image).'" alt="'.$alt.'" loading="lazy">' : '';
            $position = ($block['layout'] ?? 'image-left') === 'image-right' ? ' image-right' : '';
            return '<section class="'.$shell.'"><div class="pb-split'.$position.'"><div class="pb-media">'.$media.'</div><div class="pb-block-inner '.$align.'">'.$kicker.$heading.$copy.$button.'</div></div></section>';
        }
        if ($type === 'cards') {
            $items = is_array($block['items'] ?? null) ? $block['items'] : [];
            $columns = min(4, max(2, (int) ($block['columns'] ?? 3)));
            $cards = '';
            foreach ($items as $card) {
                if (!is_array($card)) continue;
                $cardTitle = e($card['title'] ?? 'Feature');
                $cardContent = e($card['content'] ?? '');
                $cardImage = $safeUrl($card['image'] ?? '');
                $media = $cardImage !== '' ? '<img src="'.e($cardImage).'" alt="'.$cardTitle.'" loading="lazy">' : '';
                $cards .= '<article class="pb-card-item">'.$media.'<h3>'.$cardTitle.'</h3><p>'.$cardContent.'</p></article>';
            }
            return '<section class="'.$shell.'"><div class="pb-block-inner '.$align.'">'.$kicker.$heading.$copy.'</div><div class="pb-cards-grid" style="grid-template-columns:repeat('.$columns.',minmax(0,1fr))">'.$cards.'</div></section>';
        }
        if ($type === 'stats') {
            $items = is_array($block['items'] ?? null) ? $block['items'] : [];
            $stats = '';
            foreach ($items as $stat) {
                if (!is_array($stat)) continue;
                $stats .= '<div class="pb-stat"><strong>'.e($stat['value'] ?? '—').'</strong><span>'.e($stat['label'] ?? '').'</span></div>';
            }
            return '<section class="'.$shell.'"><div class="pb-block-inner '.$align.'">'.$kicker.$heading.$copy.'</div><div class="pb-stats">'.$stats.'</div></section>';
        }
        if ($type === 'cta') return '<section class="'.$shell.'"><div class="pb-cta '.$align.'">'.$kicker.$heading.$copy.$button.'</div></section>';
        if ($type === 'video') {
            $videoUrl = $safeUrl($block['url'] ?? '');
            $video = $videoUrl !== '#' && $videoUrl !== '' ? '<iframe src="'.e($videoUrl).'" title="'.($title ?: 'Page video').'" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' : '<div class="pb-video-placeholder"><i class="fa-solid fa-circle-play"></i> Video URL not configured</div>';
            return '<section class="'.$shell.'"><div class="pb-video">'.$video.'</div>'.(($heading !== '' || $copy !== '') ? '<div class="pb-block-inner '.$align.'">'.$heading.$copy.'</div>' : '').'</section>';
        }
        return '';
    };
@endphp

@section('content')
<style>
.pb-public{--pb-max:var(--public-max,1280px);--pb-gutter:var(--public-gutter,16px);width:min(var(--pb-max),calc(100% - (var(--pb-gutter) * 2)));margin:0 auto;padding:clamp(28px,5vw,72px) 0 clamp(46px,7vw,96px)}.pb-public-intro{position:relative;overflow:hidden;margin-bottom:18px;padding:clamp(26px,5vw,58px);border:1px solid color-mix(in srgb,var(--public-accent) 17%,transparent);border-radius:var(--public-radius,16px);background:linear-gradient(135deg,color-mix(in srgb,var(--public-surface) 94%,transparent),color-mix(in srgb,var(--public-bg) 92%,transparent));box-shadow:0 22px 60px rgba(0,0,0,.16)}.pb-public-kicker{position:relative;color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.pb-public-title{position:relative;max-width:980px;margin:8px 0 10px;color:var(--public-text);font-size:clamp(30px,6vw,68px);line-height:1.02;letter-spacing:-1.5px}.pb-public-excerpt{position:relative;max-width:780px;color:var(--public-muted);font-size:clamp(14px,2vw,18px);line-height:1.7}.pb-public-sections{display:grid;gap:18px}.pb-block{overflow:hidden;border:1px solid color-mix(in srgb,var(--public-accent) 13%,transparent);border-radius:var(--public-radius,16px);background:linear-gradient(145deg,color-mix(in srgb,var(--public-surface) 96%,transparent),color-mix(in srgb,var(--public-bg) 96%,transparent));box-shadow:0 18px 50px rgba(0,0,0,.1)}.pb-block-inner{max-width:900px;padding:clamp(24px,5vw,52px)}.pb-block.tone-accent{border-color:color-mix(in srgb,var(--public-accent) 27%,transparent);background:linear-gradient(145deg,color-mix(in srgb,var(--public-accent) 10%,var(--public-surface)),color-mix(in srgb,var(--public-surface) 95%,transparent))}.pb-block.tone-light{background:linear-gradient(145deg,#102a34,#0a202a)}.pb-block.center{text-align:center}.pb-block.right{text-align:right}.pb-block-kicker{color:var(--public-accent);font-size:10px;font-weight:900;letter-spacing:.15em;text-transform:uppercase}.pb-block-title{margin:7px 0 10px;color:var(--public-text);font-size:clamp(23px,4vw,42px);line-height:1.08;letter-spacing:-.6px}.pb-copy{color:var(--public-muted);font-size:clamp(14px,1.8vw,17px);line-height:1.8}.pb-copy p{margin:0 0 1em}.pb-copy p:last-child{margin-bottom:0}.pb-copy h2,.pb-copy h3,.pb-copy h4{color:var(--public-text);line-height:1.2}.pb-copy a{color:var(--public-accent)}.pb-copy blockquote{margin:18px 0;padding:14px 18px;border-left:3px solid var(--public-accent);background:rgba(255,255,255,.025);color:#b9d0d7}.pb-button{display:inline-flex;align-items:center;justify-content:center;min-height:46px;margin-top:18px;padding:0 18px;border-radius:11px;background:linear-gradient(135deg,var(--public-accent),color-mix(in srgb,var(--public-accent) 70%,#087b93));color:#031018!important;text-decoration:none!important;font-weight:900;font-size:12px;box-shadow:0 10px 28px color-mix(in srgb,var(--public-accent) 14%,transparent)}.pb-hero{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(280px,.92fr);align-items:stretch}.pb-hero .pb-block-inner{display:flex;flex-direction:column;justify-content:center}.pb-media{min-height:320px;background:rgba(255,255,255,.025)}.pb-media img{display:block;width:100%;height:100%;min-height:320px;object-fit:cover}.pb-split{display:grid;grid-template-columns:1fr 1fr;align-items:stretch}.pb-split.image-right .pb-block-inner{order:2}.pb-split.image-right .pb-media{order:1}.pb-split .pb-media{min-height:300px}.pb-image img{display:block;width:100%;max-height:620px;object-fit:cover}.pb-cards-grid{display:grid;gap:12px;padding:0 clamp(18px,4vw,38px) clamp(18px,4vw,38px)}.pb-card-item{padding:20px;border:1px solid color-mix(in srgb,var(--public-accent) 11%,transparent);border-radius:13px;background:rgba(255,255,255,.018)}.pb-card-item h3{margin:0 0 7px;color:var(--public-text);font-size:17px}.pb-card-item p{margin:0;color:var(--public-muted);font-size:13px;line-height:1.65}.pb-card-item img{display:block;height:150px;margin:-20px -20px 16px;width:calc(100% + 40px);object-fit:cover;border-radius:12px 12px 0 0}.pb-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;padding:0 clamp(18px,4vw,38px) clamp(18px,4vw,38px)}.pb-stat{text-align:center;padding:18px 10px;border:1px solid color-mix(in srgb,var(--public-accent) 12%,transparent);border-radius:12px;background:rgba(255,255,255,.018)}.pb-stat strong{display:block;color:var(--public-text);font-size:clamp(25px,4vw,40px);line-height:1}.pb-stat span{display:block;margin-top:6px;color:var(--public-muted);font-size:11px}.pb-cta{text-align:center;padding:clamp(28px,6vw,58px)}.pb-video{aspect-ratio:16/9;background:#020c12}.pb-video iframe{display:block;width:100%;height:100%;border:0}.pb-video-placeholder{display:grid;place-items:center;height:100%;color:var(--public-muted);font-size:15px}.pb-divider{height:1px;margin:10px 8%;background:linear-gradient(90deg,transparent,color-mix(in srgb,var(--public-accent) 35%,transparent),transparent)}
@media(max-width:850px){.pb-hero,.pb-split{grid-template-columns:1fr}.pb-split.image-right .pb-block-inner,.pb-split.image-right .pb-media{order:initial}.pb-media,.pb-split .pb-media{min-height:230px}.pb-media img{min-height:230px}.pb-cards-grid{grid-template-columns:1fr 1fr!important}.pb-stats{grid-template-columns:1fr 1fr}}@media(max-width:560px){.pb-public{width:min(100% - 20px,1280px);padding-top:20px}.pb-public-intro{padding:24px 20px}.pb-public-title{font-size:34px}.pb-block-inner{padding:24px 20px}.pb-cards-grid{grid-template-columns:1fr!important;padding:0 14px 14px}.pb-stats{grid-template-columns:1fr 1fr;padding:0 14px 14px}.pb-card-item{padding:17px}.pb-card-item img{margin:-17px -17px 14px;width:calc(100% + 34px)}}
</style>

<main class="pb-public">
    <header class="pb-public-intro"><div class="pb-public-kicker">{{ config('fuelfree.company.name') }}</div><h1 class="pb-public-title">{{ $item->title }}</h1>@if($item->excerpt)<div class="pb-public-excerpt">{{ $item->excerpt }}</div>@endif</header>
    <div class="pb-public-sections">@foreach($blocks as $block) @if(is_array($block)){!! $renderBlock($block) !!}@endif @endforeach</div>
</main>
@endsection
