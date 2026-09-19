@php
    $useGlobalFramework = $useGlobalFramework ?? true;
    $useGlobalHeader = $useGlobalFramework && ($useGlobalHeader ?? true);
    $useGlobalFooter = $useGlobalFramework && ($useGlobalFooter ?? true);
    $publicBrand = $brand ?? null;
    if (!$publicBrand || (is_countable($publicBrand) && count($publicBrand) === 0)) {
        $publicBrand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    }
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $themeSettings = \App\Models\SystemSetting::query()->whereIn('key',['theme.primary','theme.accent','theme.background','theme.surface','theme.text','theme.muted','theme.max_width','theme.radius','theme.base_font'])->pluck('value','key');
    $theme = ['primary'=>$themeSettings->get('theme.primary','#48d8f1'),'accent'=>$themeSettings->get('theme.accent','#72dfbf'),'background'=>$themeSettings->get('theme.background','#031018'),'surface'=>$themeSettings->get('theme.surface','#071b26'),'text'=>$themeSettings->get('theme.text','#effcff'),'muted'=>$themeSettings->get('theme.muted','#91aeb8'),'max_width'=>$themeSettings->get('theme.max_width','1280px'),'radius'=>$themeSettings->get('theme.radius','16px'),'base_font'=>$themeSettings->get('theme.base_font','Inter, system-ui, sans-serif')];
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    if (!$publicLogo) {
        $publicLogo = \App\Models\SystemSetting::query()->where('key','company.logo_path')->value('value');
    }
@endphp
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', $publicName)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @php($faviconVersion = $publicLogo ? sha1((string) $publicLogo) : 'default-v1')
    <link rel="icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <link rel="shortcut icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ route('favicon') }}?v={{ $faviconVersion }}">
    <style>
        :root{--public-max:{{$theme['max_width']}};--public-gutter:16px;--public-bg:{{$theme['background']}};--public-surface:{{$theme['surface']}};--public-line:color-mix(in srgb, {{$theme['primary']}} 18%, transparent);--public-text:{{$theme['text']}};--public-muted:{{$theme['muted']}};--public-accent:{{$theme['primary']}};--public-radius:{{$theme['radius']}};--public-space-1:4px;--public-space-2:8px;--public-space-3:12px;--public-space-4:16px;--public-space-5:24px;--public-space-6:32px;--public-space-7:48px;--public-space-8:64px}
        .public-container{width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto}
        html{font-size:16px;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        body{margin:0;font-size:1rem;line-height:1.6;background:linear-gradient(180deg,{{$theme['background']}} 0%,{{$theme['surface']}} 52%,{{$theme['background']}} 100%);color:{{$theme['text']}};min-height:100vh;font-family:{{$theme['base_font']}};font-weight:400;letter-spacing:0}
        button,input,select,textarea{font:inherit}
        @media(max-width:600px){body{font-size:1rem}}
    </style>
    @if(request()->routeIs('home'))
        <style>
            /* Desktop homepage only: use the entire content area for two wide leadership cards. */
            @media (min-width: 992px) {
                html body main.shell.home-v3 .home-section-management .management-grid {
                    display:grid !important;
                    grid-template-columns:repeat(2,minmax(0,1fr)) !important;
                    gap:24px !important;
                    width:100% !important;
                    max-width:none !important;
                    margin-left:0 !important;
                    margin-right:0 !important;
                    justify-content:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-card {
                    display:grid !important;
                    grid-template-columns:minmax(0,38%) minmax(0,62%) !important;
                    width:100% !important;
                    min-width:0 !important;
                    min-height:0 !important;
                    height:auto !important;
                    align-items:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-photo {
                    width:100% !important;
                    height:auto !important;
                    min-height:0 !important;
                    aspect-ratio:4 / 5 !important;
                    align-self:stretch !important;
                }
                html body main.shell.home-v3 .home-section-management .member-photo img {
                    width:100% !important;
                    height:100% !important;
                    aspect-ratio:4 / 5 !important;
                    object-fit:cover !important;
                }
                html body main.shell.home-v3 .home-section-management .member-body {
                    min-width:0 !important;
                    min-height:0 !important;
                    height:100% !important;
                    padding:22px !important;
                }
            }

        </style>
    @endif
    <style id="ff-global-framework">
        /* =========================================================
           FUELFREE GLOBAL UI FRAMEWORK
           Shared foundation for Home + all public pages.
           Brand palette: Power Green / Electric Blue / Silver.
           ========================================================= */
        :root{
            --ff-green:#35d86a;
            --ff-green-strong:#25c85b;
            --ff-green-soft:rgba(53,216,106,.10);
            --ff-green-line:rgba(53,216,106,.20);
            --ff-blue:#168dff;
            --ff-blue-soft:rgba(22,141,255,.08);
            --ff-blue-line:rgba(22,141,255,.18);
            --ff-silver:#d7dde0;
            --ff-white:#f4fff8;
            --ff-ink:#020906;
            --ff-surface:#06120d;
            --ff-surface-2:#081a12;
            --ff-muted:#91a99d;
            --ff-dim:#60796e;
            --ff-max:var(--public-max,1280px);
            --ff-gutter:var(--public-gutter,16px);
            --ff-radius:var(--public-radius,16px);
            --ff-radius-sm:12px;
            --ff-radius-lg:22px;
            --ff-shadow:0 18px 55px rgba(0,0,0,.28);
            --ff-shadow-green:0 0 28px rgba(53,216,106,.08);
            --ff-border:rgba(53,216,106,.16);
            --ff-ease:cubic-bezier(.2,.75,.2,1);
        }

        html{scroll-behavior:smooth}
        body{
            background:
                radial-gradient(circle at 8% 8%,rgba(53,216,106,.035),transparent 24%),
                radial-gradient(circle at 92% 18%,rgba(22,141,255,.025),transparent 22%),
                linear-gradient(180deg,#020906 0%,#03100b 50%,#020906 100%);
            color:var(--ff-white);
        }
        ::selection{background:rgba(53,216,106,.24);color:#fff}
        :focus-visible{outline:2px solid rgba(53,216,106,.72);outline-offset:3px}

        /* Layout */
        .ff-container{
            width:min(var(--ff-max),calc(100% - (var(--ff-gutter) * 2)));
            margin-inline:auto;
        }
        .ff-section{position:relative;padding:clamp(52px,7vw,96px) 0}
        .ff-section--compact{padding:clamp(36px,5vw,64px) 0}
        .ff-section--flush{padding-top:0;padding-bottom:0}
        .ff-stack{display:flex;flex-direction:column;gap:var(--ff-space,24px)}
        .ff-grid{display:grid;gap:var(--ff-gap,24px)}
        .ff-grid--2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .ff-grid--3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .ff-grid--4{grid-template-columns:repeat(4,minmax(0,1fr))}

        /* Section heading system */
        .ff-eyebrow{
            display:inline-flex;
            align-items:center;
            gap:8px;
            margin:0 0 10px;
            color:#72e99a;
            font-size:11px;
            font-weight:800;
            letter-spacing:.16em;
            text-transform:uppercase;
        }
        .ff-eyebrow::before{
            content:"";
            width:22px;
            height:1px;
            background:linear-gradient(90deg,var(--ff-green),var(--ff-blue));
            box-shadow:0 0 10px rgba(53,216,106,.16);
        }
        .ff-title{
            margin:0;
            color:var(--ff-white);
            font-size:clamp(30px,4vw,54px);
            line-height:1.04;
            font-weight:850;
            letter-spacing:-.035em;
            text-wrap:balance;
        }
        .ff-title span,
        .ff-gradient-text{
            background:linear-gradient(105deg,#35d86a 0%,#b4f4c7 42%,#fff 76%,#74baff 100%);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
        }
        .ff-lead{
            max-width:720px;
            margin:14px 0 0;
            color:#91a99d;
            font-size:clamp(14px,1.5vw,17px);
            line-height:1.8;
        }

        /* Surfaces / cards */
        .ff-surface{
            position:relative;
            background:linear-gradient(145deg,rgba(8,26,18,.88),rgba(3,13,9,.92));
            border:1px solid var(--ff-border);
            border-radius:var(--ff-radius);
            box-shadow:var(--ff-shadow);
            overflow:hidden;
        }
        .ff-surface::before{
            content:"";
            position:absolute;
            left:0;
            right:0;
            top:0;
            height:1px;
            background:linear-gradient(90deg,transparent,rgba(53,216,106,.45),rgba(22,141,255,.18),transparent);
            pointer-events:none;
        }
        .ff-card{
            position:relative;
            padding:24px;
            background:linear-gradient(145deg,rgba(8,26,18,.78),rgba(3,13,9,.88));
            border:1px solid rgba(53,216,106,.14);
            border-radius:var(--ff-radius);
            box-shadow:0 14px 42px rgba(0,0,0,.22);
            transition:transform .22s var(--ff-ease),border-color .22s ease,box-shadow .22s ease;
        }
        .ff-card:hover{
            transform:translateY(-3px);
            border-color:rgba(53,216,106,.28);
            box-shadow:0 20px 52px rgba(0,0,0,.28),var(--ff-shadow-green);
        }
        .ff-card__icon{
            width:44px;
            height:44px;
            display:grid;
            place-items:center;
            margin-bottom:16px;
            border:1px solid rgba(53,216,106,.20);
            border-radius:13px;
            background:linear-gradient(145deg,rgba(53,216,106,.10),rgba(22,141,255,.035));
            color:#72e99a;
        }

        /* Buttons */
        .ff-btn{
            --btn-color:var(--ff-green);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:9px;
            min-height:44px;
            padding:10px 17px;
            border:1px solid rgba(53,216,106,.25);
            border-radius:12px;
            background:linear-gradient(135deg,rgba(53,216,106,.13),rgba(22,141,255,.045));
            color:var(--ff-white)!important;
            font-size:13px;
            font-weight:800;
            line-height:1.2;
            text-decoration:none!important;
            cursor:pointer;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.045);
            transition:transform .2s var(--ff-ease),background .2s ease,border-color .2s ease,box-shadow .2s ease;
        }
        .ff-btn:hover,.ff-btn:focus-visible{
            transform:translateY(-2px);
            border-color:rgba(53,216,106,.48);
            background:linear-gradient(135deg,rgba(53,216,106,.19),rgba(22,141,255,.07));
            box-shadow:0 10px 28px rgba(0,0,0,.22),0 0 20px rgba(53,216,106,.08);
        }
        .ff-btn--primary{
            color:#03100a!important;
            background:linear-gradient(135deg,#35d86a,#8ee9a9);
            border-color:rgba(142,233,169,.75);
            box-shadow:0 10px 28px rgba(53,216,106,.14);
        }
        .ff-btn--primary:hover,.ff-btn--primary:focus-visible{
            background:linear-gradient(135deg,#55e27f,#b0f3c4);
            box-shadow:0 14px 34px rgba(53,216,106,.18);
        }
        .ff-btn--secondary{
            border-color:rgba(22,141,255,.30);
            background:linear-gradient(135deg,rgba(22,141,255,.10),rgba(53,216,106,.045));
        }
        .ff-btn--ghost{
            border-color:rgba(215,221,224,.14);
            background:rgba(255,255,255,.025);
            color:var(--ff-silver)!important;
        }

        /* Badges / dividers */
        .ff-badge{
            display:inline-flex;
            align-items:center;
            gap:7px;
            min-height:28px;
            padding:5px 10px;
            border:1px solid rgba(53,216,106,.18);
            border-radius:999px;
            background:rgba(53,216,106,.055);
            color:#a8efbb;
            font-size:11px;
            font-weight:750;
            letter-spacing:.03em;
        }
        .ff-divider{
            width:100%;
            height:1px;
            margin:0;
            border:0;
            background:linear-gradient(90deg,transparent,rgba(53,216,106,.22),rgba(22,141,255,.12),transparent);
        }
        .ff-glow-line{
            position:relative;
            height:1px;
            background:linear-gradient(90deg,transparent,rgba(53,216,106,.72),rgba(22,141,255,.34),transparent);
            box-shadow:0 0 16px rgba(53,216,106,.10);
        }

        /* Forms */
        .ff-field{display:flex;flex-direction:column;gap:7px}
        .ff-label{color:#c8d7cf;font-size:12px;font-weight:750}
        .ff-input,.ff-select,.ff-textarea{
            width:100%;
            box-sizing:border-box;
            min-height:44px;
            padding:10px 12px;
            border:1px solid rgba(53,216,106,.15);
            border-radius:11px;
            background:rgba(2,10,7,.68);
            color:var(--ff-white);
            transition:border-color .18s ease,box-shadow .18s ease,background .18s ease;
        }
        .ff-textarea{min-height:120px;resize:vertical}
        .ff-input::placeholder,.ff-textarea::placeholder{color:#61796e}
        .ff-input:focus,.ff-select:focus,.ff-textarea:focus{
            border-color:rgba(53,216,106,.48);
            background:rgba(3,15,10,.82);
            box-shadow:0 0 0 3px rgba(53,216,106,.07),0 0 22px rgba(53,216,106,.045);
            outline:none;
        }

        /* Tables / notices */
        .ff-table-wrap{overflow:auto;border:1px solid rgba(53,216,106,.14);border-radius:14px;background:rgba(3,13,9,.72)}
        .ff-table{width:100%;border-collapse:collapse;color:#b8c9c0;font-size:13px}
        .ff-table th,.ff-table td{padding:12px 14px;border-bottom:1px solid rgba(53,216,106,.08);text-align:left}
        .ff-table th{color:#effff5;font-weight:800;background:rgba(53,216,106,.045)}
        .ff-table tr:last-child td{border-bottom:0}
        .ff-alert{
            padding:13px 15px;
            border:1px solid rgba(53,216,106,.18);
            border-left:3px solid var(--ff-green);
            border-radius:12px;
            background:rgba(53,216,106,.055);
            color:#b9cec2;
        }

        /* =========================================================
           Reusable page/card primitives
           Use these instead of page-specific card CSS.
           ========================================================= */
        .ff-page{width:100%;min-width:0}
        .ff-page-header{
            padding:clamp(38px,6vw,76px) 0 clamp(28px,4vw,48px);
            border-bottom:1px solid rgba(53,216,106,.09);
            background:
                radial-gradient(circle at 12% 20%,rgba(53,216,106,.055),transparent 30%),
                radial-gradient(circle at 88% 10%,rgba(22,141,255,.035),transparent 28%);
        }
        .ff-page-header__inner{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:end}
        .ff-page-header__actions{display:flex;align-items:center;justify-content:flex-end;gap:9px;flex-wrap:wrap}
        .ff-page-header__actions .ff-btn{width:auto}
        .ff-section-head{display:flex;align-items:end;justify-content:space-between;gap:24px;margin-bottom:24px}
        .ff-section-head__copy{min-width:0}
        .ff-section-head__actions{display:flex;align-items:center;justify-content:flex-end;gap:9px;flex-wrap:wrap}
        .ff-section-head__actions .ff-btn{width:auto}

        .ff-card-grid{display:grid;grid-template-columns:repeat(var(--ff-columns,3),minmax(0,1fr));gap:var(--ff-gap,24px)}
        .ff-card-grid--2{--ff-columns:2}
        .ff-card-grid--3{--ff-columns:3}
        .ff-card-grid--4{--ff-columns:4}
        .ff-card-grid--auto{grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr))}
        .ff-card--compact{padding:18px}
        .ff-card--feature{min-height:220px}
        .ff-card--interactive{cursor:pointer}
        .ff-card--interactive:hover{text-decoration:none}
        .ff-card--media{padding:0;overflow:hidden}
        .ff-card__media{display:block;width:100%;aspect-ratio:16/9;object-fit:cover;background:#07150f}
        .ff-card__body{padding:22px}
        .ff-card__eyebrow{display:block;margin:0 0 8px;color:#72e99a;font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
        .ff-card__title{margin:0;color:var(--ff-white);font-size:18px;line-height:1.25;font-weight:800;letter-spacing:-.01em}
        .ff-card__text{margin:9px 0 0;color:#91a99d;font-size:13px;line-height:1.75}
        .ff-card__meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:13px;color:#6f897d;font-size:11px;line-height:1.5}
        .ff-card__footer{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:18px;padding-top:15px;border-top:1px solid rgba(53,216,106,.09)}
        .ff-card__link{display:inline-flex;align-items:center;gap:7px;color:#9aefb1;font-size:12px;font-weight:800;text-decoration:none}
        .ff-card__link:hover{color:#fff}
        .ff-icon{
            width:44px;height:44px;display:grid;place-items:center;flex:0 0 44px;
            border:1px solid rgba(53,216,106,.20);border-radius:13px;
            background:linear-gradient(145deg,rgba(53,216,106,.10),rgba(22,141,255,.035));color:#72e99a;
        }
        .ff-icon--sm{width:36px;height:36px;flex-basis:36px;border-radius:10px;font-size:13px}
        .ff-icon--lg{width:54px;height:54px;flex-basis:54px;border-radius:15px;font-size:18px}
        .ff-stat{
            position:relative;padding:22px;border:1px solid rgba(53,216,106,.14);border-radius:var(--ff-radius);
            background:linear-gradient(145deg,rgba(8,26,18,.78),rgba(3,13,9,.88));overflow:hidden;
        }
        .ff-stat__value{display:block;margin-top:9px;color:var(--ff-white);font-size:clamp(24px,3vw,34px);line-height:1;font-weight:850;letter-spacing:-.03em}
        .ff-stat__label{display:block;margin-top:8px;color:#718b7f;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
        .ff-stat::after{content:"";position:absolute;width:100px;height:100px;right:-50px;top:-50px;border-radius:50%;background:rgba(53,216,106,.045);pointer-events:none}
        .ff-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
        .ff-actions .ff-btn{width:auto}
        .ff-prose{max-width:820px;color:#9aada3;font-size:14px;line-height:1.9}
        .ff-prose h2,.ff-prose h3{color:var(--ff-white);line-height:1.2}
        .ff-prose a{color:#8ce9a8;text-decoration:underline;text-decoration-color:rgba(53,216,106,.35);text-underline-offset:3px}
        .ff-empty{padding:38px 24px;text-align:center;border:1px dashed rgba(53,216,106,.16);border-radius:var(--ff-radius);background:rgba(53,216,106,.025);color:#718b7f}
        .ff-status{display:inline-flex;align-items:center;gap:7px;min-height:27px;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:800}
        .ff-status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;box-shadow:0 0 9px currentColor}
        .ff-status--success{color:#76e99a;background:rgba(53,216,106,.07)}
        .ff-status--info{color:#7abaff;background:rgba(22,141,255,.07)}
        .ff-status--muted{color:#879c92;background:rgba(215,221,224,.06)}
        .ff-skeleton{background:linear-gradient(90deg,rgba(255,255,255,.025),rgba(53,216,106,.055),rgba(255,255,255,.025));background-size:200% 100%;animation:ffSkeleton 1.5s linear infinite}
        @keyframes ffSkeleton{to{background-position:-200% 0}}

        /* Public legacy aliases: common page card names now inherit the framework.
           This reduces repeated per-page CSS without touching admin UI. */
        .public-site .page-card,.public-site .content-card,.public-site .info-card,.public-site .feature-card,
        .public-site .news-card,.public-site .project-card,.public-site .service-card{
            position:relative;padding:24px;background:linear-gradient(145deg,rgba(8,26,18,.78),rgba(3,13,9,.88));
            border:1px solid rgba(53,216,106,.14);border-radius:var(--ff-radius);box-shadow:0 14px 42px rgba(0,0,0,.22);
            overflow:hidden;transition:transform .22s var(--ff-ease),border-color .22s ease,box-shadow .22s ease;
        }
        .public-site .page-card:hover,.public-site .content-card:hover,.public-site .info-card:hover,.public-site .feature-card:hover,
        .public-site .news-card:hover,.public-site .project-card:hover,.public-site .service-card:hover{
            transform:translateY(-3px);border-color:rgba(53,216,106,.28);box-shadow:0 20px 52px rgba(0,0,0,.28),var(--ff-shadow-green);
        }
        .public-site .page-card::before,.public-site .content-card::before,.public-site .info-card::before,.public-site .feature-card::before,
        .public-site .news-card::before,.public-site .project-card::before,.public-site .service-card::before{
            content:"";position:absolute;left:0;right:0;top:0;height:1px;
            background:linear-gradient(90deg,transparent,rgba(53,216,106,.45),rgba(22,141,255,.18),transparent);pointer-events:none;
        }

        /* Responsive helpers */
        .ff-hide-mobile{display:block}
        .ff-only-mobile{display:none}
        @media(max-width:900px){
            .ff-grid--3,.ff-grid--4{grid-template-columns:repeat(2,minmax(0,1fr))}
            .ff-section{padding:clamp(44px,7vw,72px) 0}
        }
        @media(max-width:650px){
            .ff-grid--2,.ff-grid--3,.ff-grid--4{grid-template-columns:1fr}
            .ff-section{padding:42px 0}
            .ff-card{padding:20px}
            .ff-hide-mobile{display:none!important}
            .ff-only-mobile{display:block}
            .ff-btn{width:100%}
            .ff-title{font-size:clamp(28px,9vw,40px)}
            .ff-lead{font-size:14px}
        }
        @media(prefers-reduced-motion:reduce){
            html{scroll-behavior:auto}
            .ff-card,.ff-btn{transition:none!important}
        }
    </style>
    @stack('head')
    <style>
        /* Responsive navigation tree: compact rows, clear hierarchy, no mobile fly-outs. */
        @media (max-width:720px) {
            .public-header-nav .public-menu { gap:5px!important; }
            .public-header-nav .public-menu > .public-menu-dropdown { display:block!important;width:100%!important;margin:0!important;position:relative!important; }
            .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle,
            .public-header-nav .public-menu > a { width:100%!important;min-height:46px!important;height:46px!important;box-sizing:border-box!important;border-radius:11px!important;padding:0 13px!important; }
            .public-header-nav .public-menu-dropdown-panel,
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel {
                position:static!important;inset:auto!important;transform:none!important;width:auto!important;min-width:0!important;max-width:none!important;
                margin:4px 0 0 9px!important;padding:0 0 0 10px!important;background:transparent!important;border:0!important;border-left:1px solid rgba(86,210,238,.22)!important;border-radius:0!important;box-shadow:none!important;
            }
            .public-header-nav .public-menu-dropdown-panel { display:none;opacity:1!important;visibility:visible!important;pointer-events:auto!important; }
            .public-header-nav .public-menu-dropdown.is-open > .public-menu-dropdown-panel { display:block!important; }
            .public-header-nav .public-menu-dropdown-panel > a,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle {
                display:flex!important;align-items:center!important;width:100%!important;min-height:38px!important;height:auto!important;box-sizing:border-box!important;
                justify-content:flex-start!important;margin:2px 0!important;padding:8px 10px!important;border:0!important;border-radius:7px!important;background:transparent!important;color:#9db8c1!important;font-size:13px!important;line-height:1.3!important;text-align:left!important;
            }
            .public-header-nav .public-menu-dropdown-panel > a::before,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle::before { content:"";width:5px;height:5px;flex:0 0 5px;margin-right:9px;border-radius:50%;background:rgba(81,216,240,.55); }
            .public-header-nav .public-menu-dropdown-panel > a:hover,
            .public-header-nav .public-menu-dropdown-panel > a:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:hover,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle:focus,
            .public-header-nav .public-menu-dropdown-panel > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] { background:rgba(67,209,240,.07)!important;color:#effcff!important;outline:none; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown { display:block!important;width:100%!important;position:static!important; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown > .public-menu-dropdown-toggle { width:100%!important; }
            .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-chevron { flex:0 0 auto;width:7px;height:7px;margin-left:auto; }
            .public-header-nav .public-menu > .public-menu-dropdown > .public-menu-dropdown-toggle[aria-expanded="true"] { background:rgba(67,209,240,.09)!important;border-color:rgba(86,210,238,.17)!important;color:#effcff!important; }
            .public-header-nav .mobile-portal-separator { display:block!important;height:1px!important;margin:14px 8px 10px!important;background:linear-gradient(90deg,transparent 0%,rgba(86,210,238,.16) 16%,rgba(86,210,238,.42) 50%,rgba(86,210,238,.16) 84%,transparent 100%)!important; }
            .public-header-nav .mobile-menu-portal { position:relative!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:10px!important;width:calc(100% - 8px)!important;min-height:52px!important;height:52px!important;box-sizing:border-box!important;margin:0 4px 4px!important;padding:0 17px!important;overflow:hidden!important;border:1px solid rgba(98,217,238,.38)!important;border-radius:13px!important;background:linear-gradient(135deg,rgba(72,216,241,.14),rgba(72,216,241,.045) 55%,rgba(114,223,191,.055))!important;color:#effcff!important;font-size:15px!important;line-height:1!important;font-weight:800!important;letter-spacing:.01em!important;text-decoration:none!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 8px 22px rgba(0,0,0,.16)!important;isolation:isolate;transition:border-color .18s ease,background .18s ease,transform .18s ease,box-shadow .18s ease!important; }
            .public-header-nav .mobile-menu-portal::before { content:"";position:absolute;inset:0;pointer-events:none;z-index:-1;background:radial-gradient(circle at 50% -80%,rgba(98,217,238,.26),transparent 58%); }
            .public-header-nav .mobile-menu-portal::after { content:"";position:absolute;left:14px;right:14px;top:0;height:1px;pointer-events:none;background:linear-gradient(90deg,transparent,rgba(255,255,255,.32),transparent);opacity:.55; }
            .public-header-nav .mobile-menu-portal i { display:grid!important;place-items:center!important;width:27px!important;height:27px!important;flex:0 0 27px!important;border:1px solid rgba(139,243,255,.28)!important;border-radius:8px!important;background:rgba(4,18,26,.42)!important;color:#8bf3ff!important;font-size:12px!important;box-shadow:0 0 0 3px rgba(86,210,238,.035)!important; }
            .public-header-nav .mobile-menu-portal span { position:relative!important;top:0!important; }
            .public-header-nav .mobile-menu-portal:hover,
            .public-header-nav .mobile-menu-portal:focus-visible { background:linear-gradient(135deg,rgba(72,216,241,.19),rgba(72,216,241,.07) 55%,rgba(114,223,191,.08))!important;border-color:rgba(98,217,238,.58)!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 10px 28px rgba(0,0,0,.22),0 0 0 3px rgba(86,210,238,.055)!important;outline:none!important; }
            .public-header-nav .mobile-menu-portal:active { transform:translateY(1px)!important; }
        }
        @media (prefers-reduced-motion:reduce) { .public-header-nav .mobile-menu-portal { transition:none!important; } }
        @media (min-width:721px) { .public-header-nav .public-menu-dropdown-panel { max-width:min(320px,calc(100vw - 24px)); } .public-header-nav .public-menu-dropdown-panel .public-menu-dropdown-panel { max-height:min(70vh,520px);overflow-y:auto; } }
    </style>
</head>
<body class="public-site">
    @if($useGlobalHeader) @include('partials.public-header', ['brand' => $publicBrand]) @endif
    @yield('content')
    @if($useGlobalFooter) @include('partials.public-footer', ['brand' => $publicBrand]) @endif
    @stack('scripts')
    <script>
        (() => {
            const cleanEscapedNewlines = () => {
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
                const nodes = [];
                while (walker.nextNode()) nodes.push(walker.currentNode);
                nodes.forEach(node => {
                    if (!node.nodeValue) return;
                    const cleaned = node.nodeValue.replaceAll('\\n', '').trim();
                    if (cleaned === '') node.remove();
                    else if (cleaned !== node.nodeValue.trim()) node.nodeValue = cleaned;
                });
            };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', cleanEscapedNewlines, { once:true });
            else cleanEscapedNewlines();
        })();
    </script>
</body>
</html>
