@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
@endphp

@section('title', $siteName)

@section('content')
<style>
:root{--bg:var(--ff-ink,#020906);--panel:var(--ff-surface,#06120d);--line:var(--ff-border,rgba(53,216,106,.16));--text:var(--ff-white,#f4fff8);--muted:var(--ff-muted,#91a99d);--cyan:var(--ff-green,#35d86a);--max:var(--ff-max,1280px)}
*{box-sizing:border-box}
html{overflow-x:hidden}
body{margin:0;overflow-x:hidden;background:linear-gradient(180deg,#020906 0%,#03100b 50%,#020906 100%);color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
a{text-decoration:none;color:inherit}
img{max-width:100%}
.shell{width:min(var(--max),calc(100% - 32px));margin-inline:auto}
.eyebrow{display:block;color:var(--cyan);font-size:11px!important;line-height:1.4;letter-spacing:.2em;text-transform:uppercase}
 .home-slider{position:relative;width:100%;margin:30px 0 0;overflow:visible}
.slider-track{position:relative;width:100%;aspect-ratio:2.35/1}
.slide{position:absolute;inset:0;display:block;opacity:0;visibility:hidden;transform:scale(1.008);transition:opacity .8s ease,transform 6s ease,visibility .8s}
.slide.is-active{opacity:1;visibility:visible;transform:scale(1)}
.slide-media{position:relative;width:100%;height:100%;overflow:hidden;background:#061923;border:1px solid rgba(83,218,240,.2);border-radius:24px}
.slide img{width:100%;height:100%;display:block;object-fit:cover}
.slide-caption{position:absolute;left:0;right:0;top:calc(100% + 7px);padding:0 10px;text-align:center;pointer-events:none;overflow:hidden}
.slide-caption strong{display:block;color:#8faeb8;font-size:9px;line-height:1.3;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.welcome{padding:76px 0 70px;position:relative;isolation:isolate}
.welcome:before,.welcome:after{content:"";position:absolute;border:1px solid rgba(72,216,241,.11);border-radius:50%;pointer-events:none;z-index:-1;animation:welcomeFloat 9s ease-in-out infinite}
.welcome:before{width:260px;height:260px;right:-100px;top:8%;box-shadow:0 0 80px rgba(72,216,241,.07)}
.welcome:after{width:150px;height:150px;left:-55px;bottom:5%;animation-delay:-3s}
@keyframes welcomeFloat{0%,100%{transform:translate3d(0,0,0) rotate(0);opacity:.5}50%{transform:translate3d(0,-12px,0) rotate(8deg);opacity:.85}}
.welcome-heading{padding-bottom:26px;border-bottom:1px solid var(--line)}
.welcome h1{max-width:980px;font-size:clamp(40px,6vw,72px);line-height:1.02;letter-spacing:-.055em;margin:12px 0 0}
.welcome h1 em{font-style:normal;color:#a9f5ff}
.welcome-rule{width:58px;height:2px;margin-top:18px;background:var(--cyan);opacity:.8}
.welcome-copy{max-width:960px;padding-top:28px}
.welcome-preview,.welcome-more-content{color:var(--muted);font-size:15px;line-height:1.85;overflow-wrap:anywhere}
.welcome-more-content{margin-top:16px}
.welcome-more-toggle{display:inline-flex;align-items:center;gap:8px;margin-top:17px;padding:10px 14px;border:1px solid var(--line);border-radius:999px;background:rgba(72,216,241,.05);color:var(--cyan);font-size:11px;font-weight:750;cursor:pointer}
.welcome-more-toggle i{transition:transform .2s}
.welcome-more-toggle[aria-expanded=true] i{transform:rotate(180deg)}
.welcome-signoff{margin-top:23px;color:var(--text);font-weight:750;font-size:13px;line-height:1.5}
.welcome-signoff span{color:var(--muted);font-weight:500}
.section{padding:58px 0;border-top:1px solid rgba(83,218,240,.08);animation:sectionReveal .7s ease both}
@keyframes sectionReveal{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.section-layout-center .head{text-align:center}.section-layout-center .head>div{margin-inline:auto}
.section-layout-right .head{text-align:right}.section-layout-right .head>div{margin-left:auto}
.section-layout-left.home-section-hero .slider-track{margin-left:0;margin-right:auto}.section-layout-center.home-section-hero .slider-track{margin-inline:auto}.section-layout-right.home-section-hero .slider-track{margin-left:auto;margin-right:0}
.section-layout-center .welcome-heading,.section-layout-center .welcome-copy{text-align:center;margin-inline:auto}.section-layout-right .welcome-heading,.section-layout-right .welcome-copy{margin-left:auto;text-align:right}
.section-layout-center .cta-card{margin-inline:auto}.section-layout-right .cta-card{margin-left:auto}

/* Homepage section alignment must control the content grid as well as its heading. */
.section-layout-left .management-grid,
.section-layout-left .project-grid,
.section-layout-left .news-grid,
.section-layout-left .folders,
.section-layout-left .stats-grid,
.section-layout-left .cta-card{
    justify-content:start;
}
.section-layout-center .management-grid,
.section-layout-center .project-grid,
.section-layout-center .news-grid,
.section-layout-center .folders,
.section-layout-center .stats-grid,
.section-layout-center .cta-card{
    justify-content:center;
}
.section-layout-right .management-grid,
.section-layout-right .project-grid,
.section-layout-right .news-grid,
.section-layout-right .folders,
.section-layout-right .stats-grid,
.section-layout-right .cta-card{
    justify-content:end;
}

/* Centered executive cards should form a compact, balanced row instead of
   occupying empty grid tracks from the left edge. */
@media(min-width:651px){
    .section-layout-center .management-grid,
    .section-layout-center .project-grid,
    .section-layout-center .news-grid,
    .section-layout-center .folders{
        width:fit-content;
        max-width:100%;
    }
}

.head{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:24px}
.head>div{min-width:0}.head h2{font-size:clamp(27px,4vw,44px);line-height:1.05;letter-spacing:-.04em;margin:5px 0 0}
.head p{max-width:560px;margin:7px 0 0;color:var(--muted);font-size:11px!important;line-height:1.6!important}
.more{flex:0 0 auto;color:var(--cyan);font-size:11px!important;font-weight:700;white-space:nowrap}
.stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
.stat-card{min-width:0;padding:22px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .2s,border-color .2s}
.stat-card:hover{transform:translateY(-3px);border-color:rgba(72,216,241,.35)}
.stat-card i{color:var(--cyan);font-size:18px}.stat-card strong{display:block;margin-top:16px;font-size:28px;line-height:1.15;letter-spacing:-.03em;overflow-wrap:anywhere}.stat-card span{display:block;margin-top:7px;color:#789aa5;font-size:10px;line-height:1.4}
.project-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.project-card,.member-card,.folder{min-width:0;border:1px solid var(--line);border-radius:18px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}
.project-card:hover,.member-card:hover,.folder:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4);box-shadow:0 16px 38px rgba(0,0,0,.15)}
.project-media{height:180px;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:30px;overflow:hidden}
.project-media img,.member-photo img,.folder-media img{width:100%;height:100%;object-fit:cover;display:block}
.project-body{padding:16px}.project-top{display:flex;justify-content:space-between;gap:8px;color:#72dfbf;font-size:8px;text-transform:uppercase;letter-spacing:.12em}.project-top i{color:var(--cyan)}.project-body h3{font-size:17px;line-height:1.3;margin:10px 0 7px}.project-body p{color:var(--muted);font-size:10px;line-height:1.5;margin:0 0 13px}.project-body>strong{font-size:12px;color:#a9f5ff}
.management-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
.member-photo{aspect-ratio:1/1;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:34px;overflow:hidden}
.member-card>div:last-child{padding:14px}.member-card h3{margin:0;font-size:14px;line-height:1.3}.member-card p{margin:6px 0 0;color:var(--muted);font-size:9px;line-height:1.5}
.news-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.news{min-width:0;display:flex;flex-direction:column;border:1px solid var(--line);border-radius:17px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}
.news:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4);box-shadow:0 16px 38px rgba(0,0,0,.15)}
.news-media{width:100%;aspect-ratio:16/9;background:#061923;overflow:hidden}.news-media img{width:100%;height:100%;object-fit:cover;display:block}.news-placeholder{height:100%;display:grid;place-items:center;color:#5e8490;font-size:26px}
.news-kind{align-self:flex-start;margin:12px 15px 0;padding:4px 7px;border-radius:999px;background:rgba(72,216,241,.06);color:var(--cyan);font-size:7px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.news-kind.notice{color:#f0c58e;background:rgba(240,197,142,.06)}
.news-body{display:flex;flex:1;min-width:0;flex-direction:column;padding:10px 15px 15px}.news h3{margin:0 0 7px;font-size:16px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news p{color:var(--muted);font-size:10px!important;line-height:1.55!important;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.news-footer{margin-top:auto;padding-top:13px;display:flex;align-items:center;justify-content:space-between;gap:8px}.date{color:#6f9aa5;font-size:9px!important}.read{color:var(--cyan);font-size:9px!important;font-weight:700;white-space:nowrap}
.folders{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}.folder-media{aspect-ratio:4/3;background:#061923;overflow:hidden}.folder-media img{transition:transform .5s ease}.folder:hover .folder-media img{transform:scale(1.035)}.folder-placeholder{height:100%;display:grid;place-items:center;color:#668792;font-size:34px}.folder-body{padding:14px 15px 16px}.folder-body h3{margin:0;color:var(--text);font-size:14px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.folder-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:10px;color:#789aa5;font-size:8px!important}.folder-meta span{display:inline-flex;align-items:center;gap:6px}.folder-meta i{color:var(--cyan)}.folder-count{color:#72dfbf;white-space:nowrap}.folder-date{white-space:nowrap}
.cta-card{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:32px;border:1px solid var(--line);border-radius:23px;background:linear-gradient(120deg,rgba(10,54,70,.95),rgba(4,24,34,.95));box-shadow:0 20px 60px rgba(0,0,0,.16)}
.cta-card h2{margin:8px 0 7px;font-size:clamp(25px,4vw,42px);line-height:1.05;letter-spacing:-.04em}.cta-card p{margin:0;color:var(--muted);font-size:11px;line-height:1.65}.cta-card .btn{flex:0 0 auto}
.empty{padding:32px;text-align:center;color:var(--muted);border:1px dashed var(--line);border-radius:17px}
@media(max-width:1099px){
.shell{width:min(var(--max),calc(100% - 28px))}
.home-slider{margin-top:22px}.slide-media{border-radius:20px}
.welcome{padding:62px 0 64px}.welcome h1{font-size:clamp(38px,6.5vw,62px)}
.stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.project-grid,.news-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.management-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.folders{grid-template-columns:repeat(3,minmax(0,1fr))}
}

@media(max-width:650px){
.shell{width:calc(100% - 22px)}
.home-slider{margin-top:7px}.slider-track{aspect-ratio:2.35/1}.slide-media{border-radius:14px}.slide-caption{top:calc(100% + 5px);padding:0 5px}.slide-caption strong{font-size:7px}
.welcome{padding:47px 0 43px}.welcome:before{width:180px;height:180px;right:-100px}.welcome:after{width:110px;height:110px;left:-65px}.welcome-heading{padding-bottom:18px}.welcome .eyebrow{font-size:8px!important;letter-spacing:.15em}.welcome h1{font-size:clamp(30px,9.3vw,44px);line-height:1.05;letter-spacing:-.045em;margin-top:9px;overflow-wrap:anywhere}.welcome-rule{width:44px;margin-top:13px}.welcome-copy{padding-top:18px}.welcome-preview,.welcome-more-content{font-size:14px;line-height:1.75}.welcome-more-toggle{font-size:9px;padding:9px 12px}.welcome-signoff{font-size:11px;margin-top:17px}
.section{padding:42px 0}.head{align-items:flex-start;gap:10px;margin-bottom:15px}.head h2{font-size:clamp(22px,7vw,31px);line-height:1.08}.head p{font-size:9px!important}.more{font-size:8px!important;padding-top:7px}
.stats-grid,.project-grid,.management-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.stat-card{padding:14px;border-radius:14px;min-height:100px}.stat-card i{font-size:14px}.stat-card strong{font-size:19px;margin-top:10px}.stat-card span{font-size:7px}.project-card,.member-card{border-radius:14px}.project-media{height:auto;aspect-ratio:4/3}.project-body{padding:10px}.project-body h3{font-size:12px;margin:7px 0 5px}.project-body p{font-size:8px;line-height:1.4;margin-bottom:8px}.project-body>strong{font-size:9px}.member-card>div:last-child{padding:10px}.member-card h3{font-size:10px}.member-card p{font-size:7px}
.news-grid{grid-template-columns:1fr;gap:9px}.news{display:grid;grid-template-columns:104px 1fr;grid-template-rows:auto 1fr;min-height:104px}.news-media{grid-row:1/3;width:104px;height:104px;aspect-ratio:1/1}.news-kind{margin:9px 10px 0;font-size:6px;padding:3px 5px}.news-body{padding:6px 10px 9px}.news h3{font-size:12px;line-height:1.3;margin:0;-webkit-line-clamp:2}.news p{display:none}.news-footer{padding-top:4px}.date,.read{font-size:7px!important}.folders{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.folder{border-radius:14px}.folder-body{padding:9px 9px 10px}.folder-body h3{font-size:10px}.folder-meta{display:block;font-size:7px!important;margin-top:7px}.folder-count{display:block;margin-top:3px}.folder-meta span{gap:4px}.cta-card{display:block;padding:20px;border-radius:17px}.cta-card h2{font-size:24px}.cta-card p{font-size:9px}.cta-card .btn{display:inline-flex;margin-top:18px;min-height:40px}
}
@media(max-width:380px){
.shell{width:calc(100% - 18px)}.welcome{padding-top:41px}.welcome h1{font-size:29px}.welcome-preview,.welcome-more-content{font-size:13px}.stats-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.project-grid,.management-grid{grid-template-columns:1fr}.stat-card{min-height:108px;padding:13px}.stat-card strong{font-size:18px}.project-media{aspect-ratio:16/10}.news{grid-template-columns:94px 1fr;min-height:94px}.news-media{width:94px;height:94px}.folders{grid-template-columns:1fr}.head h2{font-size:23px}
}

/* FINAL: Board of Directors stays two-up on every mobile viewport. */
@media(max-width:650px){
    .home-section-management .management-grid{
        display:grid !important;
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        gap:10px !important;
        width:100% !important;
        max-width:none !important;
    }
    .home-section-management .member-card{
        width:100% !important;
        min-width:0 !important;
        max-width:none !important;
    }
    .home-section-management .member-photo{
        width:100%;
        aspect-ratio:4/3;
    }
}
@media(max-width:380px){
    .home-section-management .management-grid{
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        gap:8px !important;
    }
    .home-section-management .member-body{padding:9px !important}
    .home-section-management .member-body h3{font-size:11px !important}
    .home-section-management .member-role,
    .home-section-management .member-contact{font-size:7px !important}
    .home-section-management .member-message{font-size:7px !important}
    .home-section-management .member-more{font-size:7px !important;padding:8px !important}
}

@media(prefers-reduced-motion:reduce){.slide,.welcome:before,.welcome:after,.section,.project-card,.member-card,.folder,.news{animation:none;transition:none}.slide.is-active{transform:none}}

/* Management cards — shared homepage visual system. */
.home-section-management .member-card{
    display:flex;
    flex-direction:column;
    min-width:0;
    height:100%;
    border-radius:18px;
    background:linear-gradient(145deg,rgba(8,37,50,.96),rgba(3,19,27,.98));
}
.home-section-management .member-photo{
    aspect-ratio:4/3;
    flex:0 0 auto;
    background:#061923;
}
.home-section-management .member-body{
    display:flex;
    flex:1;
    min-width:0;
    flex-direction:column;
    padding:16px;
}
.home-section-management .member-body h3{
    font-size:15px;
    line-height:1.25;
    overflow-wrap:anywhere;
}
.home-section-management .member-role{
    margin-top:5px;
    color:var(--cyan);
    font-size:9px;
    font-weight:800;
    letter-spacing:.11em;
    text-transform:uppercase;
}
.home-section-management .member-contacts{
    display:grid;
    gap:5px;
    margin-top:12px;
    padding-top:11px;
    border-top:1px solid rgba(83,218,240,.13);
}
.home-section-management .member-contact{
    display:flex;
    min-width:0;
    align-items:center;
    gap:7px;
    color:#8faeb8;
    font-size:8px;
    line-height:1.35;
}
.home-section-management .member-contact i{width:12px;flex:0 0 12px;color:var(--cyan);text-align:center}
.home-section-management .member-contact span{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.home-section-management .member-message{
    margin-top:12px!important;
    padding-top:11px;
    border-top:1px solid rgba(83,218,240,.13);
    color:#9ab7bf !important;
    font-size:9px!important;
    line-height:1.55!important;
    display:-webkit-box;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:3;
    overflow:hidden;
}
.home-section-management .member-message:before{content:"\f075";margin-right:7px;color:var(--cyan);font-family:"Font Awesome 6 Free";font-weight:900}
.home-section-management .member-more{
    width:100%;
    min-height:38px;
    margin-top:auto;
    padding:9px 12px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    border:1px solid rgba(72,216,241,.22);
    border-radius:11px;
    background:rgba(72,216,241,.035);
    color:var(--cyan);
    font-size:9px;
    font-weight:800;
    cursor:pointer;
    transition:background .2s ease,border-color .2s ease,transform .2s ease;
}
.home-section-management .member-more:hover{border-color:rgba(72,216,241,.48);background:rgba(72,216,241,.08);transform:translateY(-1px)}
@media(max-width:650px){
    .home-section-management .member-photo{aspect-ratio:4/3}
    .home-section-management .member-body{padding:13px}
    .home-section-management .member-body h3{font-size:14px}
    .home-section-management .member-role{font-size:8px}
    .home-section-management .member-contact{font-size:8px}
    .home-section-management .member-message{font-size:8px!important}
}
</style>
<style>
/* === HOMEPAGE MANAGEMENT PROFILE CARD V4 === */
.home-v3 .home-section-management .management-grid{width:100%!important;display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:clamp(12px,1.8vw,20px)!important;align-items:stretch!important}
.home-v3 .home-section-management .member-card{min-width:0!important;width:100%!important;max-width:none!important;height:auto!important;display:grid!important;overflow:hidden!important;border-radius:20px!important;grid-template-columns:minmax(120px,42%) minmax(0,58%)!important;grid-template-rows:1fr!important;align-items:stretch!important}
.home-v3 .home-section-management .member-photo{grid-column:1!important;grid-row:1!important;width:100%!important;height:auto!important;aspect-ratio:4/5!important;align-self:stretch!important;overflow:hidden!important}
.home-v3 .home-section-management .member-photo img{width:100%!important;height:100%!important;object-fit:cover!important}
.home-v3 .home-section-management .member-body{grid-column:2!important;grid-row:1!important;min-width:0!important;min-height:0!important;height:100%!important;display:flex!important;flex-direction:column!important;justify-content:flex-start!important;padding:clamp(14px,1.5vw,20px)!important}
.home-v3 .home-section-management .member-body h3{margin:0!important;font-size:clamp(15px,1.35vw,20px)!important;line-height:1.25!important;text-align:left!important;overflow-wrap:anywhere!important}
.home-v3 .home-section-management .member-role{margin:5px 0 0!important;font-size:clamp(8px,.75vw,10px)!important;line-height:1.4!important;text-align:left!important}
.home-v3 .home-section-management .member-contacts{display:grid!important;gap:5px!important;margin:11px 0 0!important;padding:0!important;border:0!important}
.home-v3 .home-section-management .member-contact{display:flex!important;min-width:0!important;align-items:flex-start!important;gap:7px!important;font-size:clamp(8px,.72vw,10px)!important;line-height:1.4!important;white-space:normal!important}
.home-v3 .home-section-management .member-contact span{min-width:0!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.home-v3 .home-section-management .member-message{display:-webkit-box!important;min-height:0!important;height:auto!important;max-height:4.55em!important;margin:12px 0 0!important;padding:0!important;border:0!important;font-size:clamp(9px,.78vw,11px)!important;line-height:1.52!important;-webkit-line-clamp:3!important;line-clamp:3!important;overflow:hidden!important;overflow-wrap:anywhere!important}
.home-v3 .home-section-management .member-more{flex:0 0 auto!important;width:auto!important;align-self:flex-start!important;min-height:36px!important;margin:13px 0 0!important;padding:8px 12px!important;border-radius:10px!important;justify-content:center!important;gap:8px!important;font-size:9px!important;white-space:nowrap!important}
@media(max-width:650px){.home-v3 .home-section-management .management-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:9px!important}.home-v3 .home-section-management .member-card{display:flex!important;flex-direction:column!important;grid-template-columns:none!important;grid-template-rows:none!important;border-radius:14px!important}.home-v3 .home-section-management .member-photo{width:100%!important;height:auto!important;aspect-ratio:1/1!important;flex:0 0 auto!important}.home-v3 .home-section-management .member-body{width:100%!important;height:auto!important;display:flex!important;flex:0 0 auto!important;padding:10px!important}.home-v3 .home-section-management .member-body h3{font-size:clamp(10px,3.1vw,13px)!important;text-align:center!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}.home-v3 .home-section-management .member-role{font-size:clamp(7px,2vw,9px)!important;text-align:center!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}.home-v3 .home-section-management .member-contacts{display:grid!important;gap:4px!important;margin-top:9px!important}.home-v3 .home-section-management .member-contact{font-size:clamp(7px,1.9vw,9px)!important;text-align:left!important}.home-v3 .home-section-management .member-contact span{white-space:nowrap!important}.home-v3 .home-section-management .member-message{margin-top:9px!important;font-size:clamp(7.5px,2vw,9px)!important;line-height:1.5!important;-webkit-line-clamp:3!important;line-clamp:3!important}.home-v3 .home-section-management .member-more{width:100%!important;min-height:34px!important;margin-top:9px!important;align-self:stretch!important;padding:7px!important;font-size:clamp(7px,1.9vw,9px)!important}}
@media(min-width:651px) and (max-width:1099px){.home-v3 .home-section-management .member-card{grid-template-columns:minmax(110px,38%) minmax(0,62%)!important}.home-v3 .home-section-management .member-body{padding:13px!important}}
@media(max-width:420px){.home-v3 .home-section-management .management-grid{gap:8px!important}.home-v3 .home-section-management .member-card{border-radius:12px!important}.home-v3 .home-section-management .member-body{padding:8px!important}.home-v3 .home-section-management .member-contacts{margin-top:7px!important;gap:3px!important}.home-v3 .home-section-management .member-message{margin-top:7px!important}.home-v3 .home-section-management .member-more{margin-top:7px!important;min-height:32px!important}}
/* === FFP management profile horizontal card composition — DESKTOP ONLY === */
@media (min-width: 1100px) {
    .home-v3 .home-section-management .management-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
    }
    .home-v3 .home-section-management .member-card {
        display: grid !important;
        grid-template-columns: minmax(0, 40%) minmax(0, 60%) !important;
        grid-template-rows: 1fr !important;
        height: auto !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }
    .home-v3 .home-section-management .member-photo {
        grid-column: 1 !important;
        grid-row: 1 !important;
        width: 100% !important;
        height: 100% !important;
        aspect-ratio: 4 / 5 !important;
        min-height: 0 !important;
    }
    .home-v3 .home-section-management .member-photo img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .home-v3 .home-section-management .member-body {
        grid-column: 2 !important;
        grid-row: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        min-width: 0 !important;
        padding: 22px !important;
    }
    .home-v3 .home-section-management .member-body h3 {
        margin: 0 !important;
        font-size: 18px !important;
        line-height: 1.3 !important;
    }
    .home-v3 .home-section-management .member-role {
        margin-top: 5px !important;
        font-size: 9px !important;
    }
    .home-v3 .home-section-management .member-contacts {
        margin-top: 12px !important;
        padding: 0 !important;
        border: 0 !important;
        gap: 5px !important;
    }
    .home-v3 .home-section-management .member-contact {
        font-size: 9px !important;
    }
    .home-v3 .home-section-management .member-contact i {
        display: none !important;
    }
    .home-v3 .home-section-management .member-message {
        margin-top: 12px !important;
        padding: 0 !important;
        border: 0 !important;
        font-size: 10px !important;
        line-height: 1.55 !important;
        -webkit-line-clamp: 3 !important;
        line-clamp: 3 !important;
    }
    .home-v3 .home-section-management .member-message:before {
        content: none !important;
        display: none !important;
    }
    .home-v3 .home-section-management .member-more {
        width: auto !important;
        align-self: flex-start !important;
        margin-top: 14px !important;
        min-height: 36px !important;
        padding: 8px 15px !important;
    }
}
</style>
<style>
/* === HOMEPAGE MANAGEMENT PROFILE ACCESSIBILITY POLISH === */
/* Larger reading sizes for older users; layout and controls remain unchanged. */
.home-v3 .home-section-management .member-body h3{font-size:18px!important;line-height:1.35!important}
.home-v3 .home-section-management .member-role{font-size:10px!important;line-height:1.5!important}
.home-v3 .home-section-management .member-contact{font-size:10px!important;line-height:1.5!important}
.home-v3 .home-section-management .member-message{font-size:11px!important;line-height:1.65!important}
.home-v3 .home-section-management .member-more{font-size:10px!important;min-height:40px!important}
.home-profile-title{font-size:31px!important;line-height:1.2!important}
.home-profile-role{font-size:12px!important;line-height:1.5!important}
.home-profile-message{font-size:15px!important;line-height:1.9!important}
.home-profile-section-title{font-size:10px!important;line-height:1.45!important}
.home-profile-link{font-size:11px!important;min-height:44px!important}
@media(max-width:1099px){.home-profile-title{font-size:28px!important}.home-profile-message{font-size:15px!important;line-height:1.85!important}}
@media(max-width:650px){
 .home-v3 .home-section-management .member-body h3{font-size:12px!important;line-height:1.35!important}
 .home-v3 .home-section-management .member-role{font-size:8px!important;line-height:1.45!important}
 .home-v3 .home-section-management .member-more{font-size:9px!important;min-height:36px!important}
 .home-profile-title{font-size:23px!important;line-height:1.2!important}
 .home-profile-role{font-size:10px!important;line-height:1.45!important}
 .home-profile-contact-mobile a{font-size:9px!important;line-height:1.5!important}
 .home-profile-scroll{font-size:15px!important;line-height:1.85!important}
 .home-profile-section-title{font-size:9px!important;line-height:1.45!important}
 .home-profile-message{font-size:14px!important;line-height:1.85!important}
 .home-profile-link{font-size:10px!important;min-height:42px!important}
}
@media(max-width:420px){.home-profile-title{font-size:22px!important}.home-profile-message{font-size:14px!important;line-height:1.85!important}}
\n/* FINAL MANAGEMENT CARD LAYOUT PATCH */\n@media(min-width:651px){\n  .home-section-management .management-grid{grid-template-columns:repeat(2,minmax(0,1fr)) !important;gap:18px !important;align-items:start !important;}\n  .home-section-management .member-card{display:grid !important;grid-template-columns:minmax(0,40%) minmax(0,60%) !important;align-items:stretch !important;height:auto !important;min-height:0 !important;}\n  .home-section-management .member-photo{width:100% !important;height:auto !important;aspect-ratio:4 / 5 !important;min-height:0 !important;flex:none !important;}\n  .home-section-management .member-photo img{width:100% !important;height:100% !important;object-fit:cover !important;object-position:center center !important;}\n  .home-section-management .member-body{min-width:0 !important;min-height:0 !important;height:auto !important;display:flex !important;flex-direction:column !important;justify-content:center !important;padding:22px !important;}\n  .home-section-management .member-body h3{font-size:clamp(18px,1.55vw,24px) !important;line-height:1.2 !important;text-transform:none !important;}\n  .home-section-management .member-role{font-size:11px !important;line-height:1.4 !important;text-transform:uppercase !important;}\n  .home-section-management .member-contacts{margin-top:13px !important;padding-top:0 !important;border-top:0 !important;gap:7px !important;}\n  .home-section-management .member-contact{display:flex !important;align-items:center !important;gap:9px !important;font-size:11px !important;line-height:1.45 !important;text-transform:none !important;}\n  .home-section-management .member-contact i{display:inline-flex !important;flex:0 0 16px !important;width:16px !important;justify-content:center !important;color:var(--cyan) !important;}\n  .home-section-management .member-contact span{min-width:0 !important;overflow:hidden !important;text-overflow:ellipsis !important;white-space:nowrap !important;}\n  .home-section-management .member-message{margin-top:13px !important;padding-top:0 !important;border-top:0 !important;font-size:12px !important;line-height:1.55 !important;font-weight:500 !important;text-transform:none !important;letter-spacing:normal !important;-webkit-line-clamp:3 !important;}\n  .home-section-management .member-message:before{content:none !important;display:none !important;}\n  .home-section-management .member-more{align-self:flex-start !important;width:auto !important;min-width:160px !important;min-height:42px !important;margin-top:15px !important;padding:10px 16px !important;font-size:11px !important;}\n}\n@media(max-width:650px){\n  .home-section-management .member-message{text-transform:none !important;letter-spacing:normal !important;}\n  .home-section-management .member-contact i{display:inline-flex !important;}\n}\n\n/* FINAL BOARD OF DIRECTORS CARD GEOMETRY REPAIR */\n@media(min-width:651px){\n  .home-section-management .management-grid{grid-template-columns:repeat(2,minmax(0,1fr)) !important;gap:20px !important;align-items:start !important;}\n  .home-section-management .member-card{display:grid !important;grid-template-columns:minmax(0,40%) minmax(0,60%) !important;grid-template-rows:auto !important;align-items:stretch !important;align-self:start !important;width:100% !important;height:auto !important;min-height:0 !important;aspect-ratio:2 / 1 !important;overflow:hidden !important;}\n  .home-section-management .member-photo{width:100% !important;height:100% !important;aspect-ratio:4 / 5 !important;min-height:0 !important;align-self:stretch !important;flex:none !important;}\n  .home-section-management .member-photo img{width:100% !important;height:100% !important;object-fit:cover !important;object-position:center center !important;display:block !important;}\n  .home-section-management .member-body{min-width:0 !important;min-height:0 !important;height:100% !important;display:flex !important;flex-direction:column !important;justify-content:center !important;align-self:stretch !important;padding:20px 22px !important;overflow:hidden !important;}\n  .home-section-management .member-body h3{margin:0 !important;font-size:clamp(17px,1.45vw,23px) !important;line-height:1.2 !important;text-transform:none !important;letter-spacing:-.02em !important;}\n  .home-section-management .member-role{margin-top:6px !important;font-size:9px !important;line-height:1.35 !important;text-transform:uppercase !important;}\n  .home-section-management .member-contacts{margin-top:12px !important;padding-top:0 !important;border-top:0 !important;gap:6px !important;}\n  .home-section-management .member-contact{display:flex !important;align-items:center !important;gap:8px !important;font-size:9px !important;line-height:1.35 !important;text-transform:none !important;}\n  .home-section-management .member-contact i{display:inline-flex !important;visibility:visible !important;opacity:1 !important;flex:0 0 13px !important;width:13px !important;justify-content:center !important;color:var(--cyan) !important;text-align:center !important;}\n  .home-section-management .member-contact span{min-width:0 !important;overflow:hidden !important;text-overflow:ellipsis !important;white-space:nowrap !important;}\n  .home-section-management .member-message{margin-top:12px !important;padding-top:0 !important;border-top:0 !important;color:#9ab7bf !important;font-size:9.5px !important;line-height:1.5 !important;font-weight:500 !important;text-transform:none !important;letter-spacing:normal !important;display:-webkit-box !important;-webkit-box-orient:vertical !important;-webkit-line-clamp:3 !important;overflow:hidden !important;}\n  .home-section-management .member-message:before{content:none !important;display:none !important;}\n  .home-section-management .member-more{align-self:flex-start !important;width:auto !important;min-width:160px !important;min-height:38px !important;margin-top:13px !important;padding:9px 16px !important;justify-content:center !important;font-size:9px !important;}\n}\n@media(min-width:651px) and (max-width:1099px){\n  .home-section-management .member-card{grid-template-columns:minmax(0,42%) minmax(0,58%) !important;aspect-ratio:1.9 / 1 !important;}\n  .home-section-management .member-body{padding:16px !important;}\n  .home-section-management .member-body h3{font-size:15px !important;}\n  .home-section-management .member-contact{font-size:8px !important;}\n  .home-section-management .member-message{font-size:8.5px !important;}\n}\n@media(max-width:650px){\n  .home-section-management .management-grid{grid-template-columns:repeat(2,minmax(0,1fr)) !important;gap:10px !important;}\n  .home-section-management .member-card{display:flex !important;flex-direction:column !important;aspect-ratio:auto !important;height:100% !important;overflow:hidden !important;}\n  .home-section-management .member-photo{width:100% !important;height:auto !important;aspect-ratio:1 / 1 !important;}\n  .home-section-management .member-body{height:auto !important;padding:11px !important;justify-content:flex-start !important;overflow:visible !important;text-align:left !important;}\n  .home-section-management .member-body h3{text-align:center !important;font-size:11px !important;}\n  .home-section-management .member-role{text-align:center !important;font-size:7px !important;}\n  .home-section-management .member-contacts{margin-top:9px !important;gap:5px !important;}\n  .home-section-management .member-contact{font-size:7px !important;justify-content:flex-start !important;}\n  .home-section-management .member-contact i{display:inline-flex !important;visibility:visible !important;}\n  .home-section-management .member-message{margin-top:9px !important;font-size:7px !important;line-height:1.5 !important;text-align:left !important;text-transform:none !important;letter-spacing:normal !important;-webkit-line-clamp:3 !important;}\n  .home-section-management .member-more{width:100% !important;min-height:34px !important;margin-top:10px !important;padding:8px 7px !important;font-size:7px !important;}\n}\n@media(max-width:380px){\n  .home-section-management .management-grid{gap:8px !important;}\n  .home-section-management .member-body{padding:9px !important;}\n  .home-section-management .member-body h3{font-size:10px !important;}\n  .home-section-management .member-role,.home-section-management .member-contact{font-size:6.5px !important;}\n  .home-section-management .member-message{font-size:6.5px !important;}\n  .home-section-management .member-more{font-size:6.5px !important;}\n}\n</style>
<main class="shell home-v3">
@foreach($home['section_order'] as $section)
@if($section==='hero' && $home['slider'] && $sliders->isNotEmpty())
<section class="home-slider home-section home-section-hero section-layout-{{ ($sectionSettings['hero'] ?? [])['layout'] ?? 'left' }}" aria-label="Company highlights">
<div class="slider-track">
@foreach($sliders as $index => $slider)
@php($sliderUrl=$slider->link_url)
@if($sliderUrl)<a class="slide {{ $index===0?'is-active':'' }}" href="{{ $sliderUrl }}" @if(str_starts_with($sliderUrl,'http')) target="_blank" rel="noopener" @endif>@else<div class="slide {{ $index===0?'is-active':'' }}">@endif
<div class="slide-media"><img src="{{ asset('storage/'.ltrim($slider->image_path,'/')) }}" alt="{{ $slider->title ?: $siteName }}" @if($index>0)loading="lazy"@endif></div>
@if($slider->title)<div class="slide-caption" aria-label="{{ $slider->title }}"><strong>{{ $slider->title }}</strong></div>@endif
@if($sliderUrl)</a>@else</div>@endif
@endforeach
</div>
</section>
@endif

@if($section==='welcome' && $home['welcome'])
<section class="welcome home-section home-section-welcome section-layout-{{ $welcomeLayout }}" data-welcome>
<div class="welcome-inner">
<div class="welcome-content">
<div class="welcome-heading"><span class="eyebrow">{{ $welcomeEyebrow ?: 'Welcome to '.$siteName }}</span><h1>{{ $welcomeTitle ?: 'Building a stronger energy future.' }}</h1><div class="welcome-rule"></div></div>
<div class="welcome-copy">
<div class="welcome-preview">{!! nl2br(e($welcomePreview)) !!}</div>
@if($welcomeHasMore)
<div class="welcome-more-content" hidden>{!! nl2br(e($welcomeRemaining)) !!}</div>
<button type="button" class="welcome-more-toggle" aria-expanded="false"><span>Read more</span><i class="fa-solid fa-arrow-down"></i></button>
@endif
</div>
</div>
</div>
</div>
</section>
@endif

@if($section==='management' && $home['management'])

<section class="section home-section home-section-management section-layout-{{ ($sectionSettings['management'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Leadership</span><h2>Board of Directors</h2></div><a class="more" href="{{ route('management') }}">Meet the team →</a></div>
<div class="management-grid">
@if($homeManagement->isNotEmpty())
@foreach($homeManagement as $member)
<article class="member-card" data-home-profile="{{ $member->id }}" data-card-number="{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}">
<div class="member-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user"></i>@endif</div>
<div class="member-body">
<h3>{{ $member->title }}</h3>
<p class="member-role">{{ $member->designation ?: $member->excerpt }}</p>
<div class="member-contacts" aria-label="Contact details">
@if($member->email)<a class="member-contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i><span>{{ $member->email }}</span></a>@endif
@if($member->phone)<a class="member-contact" href="tel:{{ preg_replace('/[^0-9+]/','',$member->phone) }}"><i class="fa-solid fa-phone"></i><span>{{ $member->phone }}</span></a>@endif
</div>
@if($member->content)<p class="member-message">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $member->content)), 125) }}</p>@else<p class="member-message is-empty">Leadership profile</p>@endif
<button class="member-more" type="button" data-profile="{{ $member->id }}"><span>View full message</span><i class="fa-solid fa-arrow-right"></i></button>
</div>
<div class="home-profile-data" hidden data-name="{{ $member->title }}" data-role="{{ $member->designation ?: $member->excerpt }}" data-message="{{ $member->content }}" data-image="{{ $member->image_path ? asset('storage/'.$member->image_path) : '' }}" data-phone="{{ $member->phone }}" data-email="{{ $member->email }}" data-vcard="{{ route('management.vcard',$member) }}" data-card="{{ $member->visiting_card_path ? asset('storage/'.$member->visiting_card_path) : '' }}"></div>
</article>
@endforeach
@else<div class="empty" style="grid-column:1/-1">Management profiles will appear here when published.</div>@endif
</div></section>
@endif

@if($section==='news' && $home['news'])
<section class="section home-section home-section-news section-layout-{{ ($sectionSettings['news'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Latest updates</span><h2>News &amp; Notices</h2></div><a class="more" href="{{ route('news.index') }}">View all →</a></div><div class="news-grid">@if(($content['news']??collect())->isNotEmpty())@foreach(($content['news']??collect()) as $item)<a class="news" href="{{ route('news.show',$item->slug) }}"><div class="news-media">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="news-placeholder">▣</div>@endif</div><div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':'News' }}</div><div class="news-body"><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 180) }}</p><div class="news-footer"><span class="date">{{ $item->published_at?->format('d F Y') }}</span><span class="read">Read more →</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No news has been published yet.</div>@endif</div></section>
@endif

@if($section==='gallery' && $home['gallery'])
<section class="section home-section home-section-gallery section-layout-{{ ($sectionSettings['gallery'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Photo collections</span><h2>Gallery</h2></div><a class="more" href="{{ route('site.gallery') }}">View all →</a></div><div class="folders">@if($gallery->isNotEmpty())@foreach($gallery as $item)<a class="folder" href="{{ route('gallery.show',['item'=>$item->slug ?: $item->id]) }}"><div class="folder-media">@if($item->image_path)<img src="{{ asset('storage/'.ltrim($item->image_path,'/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="folder-placeholder"><i class="fa-regular fa-images"></i></div>@endif</div><div class="folder-body"><h3>{{ $item->title }}</h3><div class="folder-meta"><span class="folder-date"><i class="fa-regular fa-calendar"></i>{{ $item->published_at?->format('d F Y') ?? $item->created_at?->format('d F Y') }}</span><span class="folder-count"><i class="fa-regular fa-images"></i>{{ $item->gallery_media_count }} {{ $item->gallery_media_count === 1 ? 'photo' : 'photos' }}</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No photo galleries have been published yet.</div>@endif</div></section>
@endif

@if($section==='cta' && $home['cta'])
<section class="section home-section home-section-cta section-layout-{{ ($sectionSettings['cta'] ?? [])['layout'] ?? 'left' }} cta-section"><div class="cta-card"><div><span class="eyebrow">Let's build the future</span><h2>Reliable energy. Responsible growth.</h2><p>{{ $brand['tagline'] }}</p></div><a class="btn" href="{{ route('contact') }}">Contact us <i class="fa-solid fa-arrow-right"></i></a></div></section>
@endif
@endforeach
</main>

<div class="home-profile-modal" id="homeProfileModal" aria-hidden="true">
    <div class="home-profile-panel" role="dialog" aria-modal="true" aria-labelledby="homeProfileTitle">
        <aside class="home-profile-identity">
            <div class="home-profile-kicker">Management Profile</div>
            <div class="home-profile-photo" id="homeProfilePhoto"></div>
            <div class="home-profile-identity-copy">
                <h2 class="home-profile-title" id="homeProfileTitle"></h2>
                <div class="home-profile-role" id="homeProfileRole"></div>
            </div>
            <div class="home-profile-contacts" id="homeProfileContacts"></div>
        </aside>
        <section class="home-profile-message-pane">
            <button class="home-profile-close" id="homeProfileClose" type="button" aria-label="Close profile"><i class="fa-solid fa-xmark"></i></button>
            <div class="home-profile-message-head">
                <div class="home-profile-section-title" id="homeProfileMessageTitle">Message from leadership</div>
                <div class="home-profile-message-rule"></div>
            </div>
            <div class="home-profile-scroll">
                <div class="home-profile-message" id="homeProfileMessage"></div>
            </div>
        </section>
        <a class="home-profile-add-contact" id="homeProfileAddContact" href="#" download>
            <i class="fa-solid fa-user-plus"></i>
            <span>Add to Contacts</span>
        </a>
    </div>
</div>

<script>
(() => {
 const modal=document.getElementById('homeProfileModal');
 if(!modal)return;
 const title=document.getElementById('homeProfileTitle'),role=document.getElementById('homeProfileRole');
 const messageTitle=document.getElementById('homeProfileMessageTitle');
 const photo=document.getElementById('homeProfilePhoto'),message=document.getElementById('homeProfileMessage');
 const contacts=document.getElementById('homeProfileContacts');
 const addContact=document.getElementById('homeProfileAddContact');
 const close=document.getElementById('homeProfileClose');
 const buttons=[...document.querySelectorAll('.home-section-management .member-more')];
 let lastTrigger=null;
 const esc=s=>String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 const renderMessage=raw=>{
   const clean=String(raw??'').trim();
   if(!clean)return '<p>No additional profile information is available.</p>';
   return clean.split(/\n\s*\n/).filter(Boolean).map(p=>'<p>'+esc(p).replace(/\n/g,'<br>')+'</p>').join('');
 };
 const closeModal=()=>{
   modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow='';
   if(lastTrigger)lastTrigger.focus();
 };
 const openModal=btn=>{
   const data=btn.closest('.member-card')?.querySelector('.home-profile-data'); if(!data)return;
   lastTrigger=btn;
   title.textContent=data.dataset.name||'';
   role.textContent=String(data.dataset.role||'').trim();
   if(messageTitle) messageTitle.textContent='Message from '+(String(data.dataset.role||'').trim()||'Leadership');
   message.innerHTML=renderMessage(data.dataset.message||'');
   photo.innerHTML=data.dataset.image
      ? '<img src="'+esc(data.dataset.image)+'" alt="'+esc(data.dataset.name||'')+'">'
      : '<div class="home-profile-photo-placeholder"><i class="fa-solid fa-user-tie"></i></div>';
   const contact=[];
   if(data.dataset.phone)contact.push('<a class="home-profile-contact" href="tel:'+esc(data.dataset.phone.replace(/[^0-9+]/g,''))+'"><i class="fa-solid fa-phone"></i><span><small>Phone</small><strong>'+esc(data.dataset.phone)+'</strong></span></a>');
   if(data.dataset.email)contact.push('<a class="home-profile-contact" href="mailto:'+esc(data.dataset.email)+'"><i class="fa-solid fa-envelope"></i><span><small>Email</small><strong>'+esc(data.dataset.email)+'</strong></span></a>');
   contacts.innerHTML=contact.join('');
   if(addContact){
      addContact.href=data.dataset.vcard||'#';
      addContact.hidden=!data.dataset.vcard;
   }
   modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; close.focus();
 };
 buttons.forEach(btn=>btn.addEventListener('click',()=>openModal(btn)));
 close.addEventListener('click',closeModal);
 modal.addEventListener('click',e=>{if(e.target===modal)closeModal()});
 document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal.classList.contains('open'))closeModal()});
})();
</script>

<style>
/* === FINAL STATIC HOMEPAGE MODE ===
   Professional visual design stays intact; motion is disabled for reliable
   touch interaction and predictable rendering on every device. */
.home-v3,
.home-v3 *,
.home-v3 *::before,
.home-v3 *::after{
    animation:none !important;
    transition:none !important;
    scroll-behavior:auto !important;
}
.home-v3 .slide,
.home-v3 .slide.is-active{
    transform:none !important;
}
.home-v3 .home-section,
.home-v3 .home-section.reveal-ready,
.home-v3 .home-section.revealed,
.home-v3 .welcome-with-team .welcome-content,
.home-v3 .welcome-with-team .welcome-profile,
.home-v3 .welcome-heading,
.home-v3 .welcome-copy,
.home-v3 .home-section .head>div,
.home-v3 .home-section .head>p{
    opacity:1 !important;
    transform:none !important;
    translate:none !important;
    will-change:auto !important;
}
.home-v3 .stat-card:hover,
.home-v3 .project-card:hover,
.home-v3 .member-card:hover,
.home-v3 .news:hover,
.home-v3 .folder:hover,
.home-v3 .welcome-profile:hover,
.home-v3 .member-more:hover{
    transform:none !important;
}
.home-v3 .slide.is-active .slide-media:after,
.home-v3 .welcome-heading:before,
.home-v3 .cta-card:before,
.home-v3 .cta-card:after,
.home-v3 .home-slider:before{
    animation:none !important;
}
@media (pointer:coarse){
    .home-v3 .home-slider,
    .home-v3 .home-section,
    .home-v3 .member-card,
    .home-v3 .stat-card,
    .home-v3 .project-card,
    .home-v3 .news,
    .home-v3 .folder,
    .home-v3 button,
    .home-v3 a{
        -webkit-tap-highlight-color:transparent;
    }
}

/* ONE-TIME DESKTOP PROFILE CARD REPAIR */
@media (min-width: 651px) {
    .home-v3.home-v3 .home-section-management .management-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
        align-items: stretch !important;
    }
    .home-v3.home-v3 .home-section-management .member-card {
        display: grid !important;
        grid-template-columns: minmax(0, 40%) minmax(0, 60%) !important;
        align-items: stretch !important;
        height: auto !important;
        min-height: 0 !important;
    }
    .home-v3.home-v3 .home-section-management .member-photo {
        width: 100% !important;
        height: 100% !important;
        min-height: 0 !important;
        aspect-ratio: 4 / 5 !important;
        align-self: stretch !important;
        overflow: hidden !important;
        display: block !important;
    }
    .home-v3.home-v3 .home-section-management .member-photo img {
        width: 100% !important;
        height: 100% !important;
        display: block !important;
        object-fit: contain !important;
        object-position: center center !important;
    }
    .home-v3.home-v3 .home-section-management .member-body {
        min-width: 0 !important;
        min-height: 100% !important;
        padding: 24px 26px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        overflow: hidden !important;
    }
    .home-v3.home-v3 .home-section-management .member-body h3 {
        font-size: clamp(20px, 1.5vw, 25px) !important;
        line-height: 1.3 !important;
    }
    .home-v3.home-v3 .home-section-management .member-role {
        margin-top: 7px !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
    }
    .home-v3.home-v3 .home-section-management .member-contacts {
        margin-top: 14px !important;
        padding-top: 0 !important;
        border-top: 0 !important;
    }
    .home-v3.home-v3 .home-section-management .member-contact {
        font-size: 12px !important;
        line-height: 1.55 !important;
    }
    .home-v3.home-v3 .home-section-management .member-message {
        margin-top: 14px !important;
        padding-top: 0 !important;
        border-top: 0 !important;
        font-size: 14px !important;
        line-height: 1.65 !important;
        font-weight: 500 !important;
    }
    .home-v3.home-v3 .home-section-management .member-message:before {
        content: none !important;
    }
    .home-v3.home-v3 .home-section-management .member-more {
        align-self: flex-start !important;
        width: auto !important;
        min-width: 210px !important;
        min-height: 48px !important;
        margin-top: 16px !important;
        font-size: 13px !important;
    }
}

@media (min-width: 651px) and (max-width: 1099px) {
    .home-v3.home-v3 .home-section-management .member-card {
        grid-template-columns: minmax(0, 42%) minmax(0, 58%) !important;
    }
    .home-v3.home-v3 .home-section-management .member-body {
        padding: 18px !important;
    }
    .home-v3.home-v3 .home-section-management .member-body h3 {
        font-size: 19px !important;
    }
    .home-v3.home-v3 .home-section-management .member-contact {
        font-size: 10px !important;
    }
    .home-v3.home-v3 .home-section-management .member-message {
        font-size: 12px !important;
    }
}
</style>

<script>
(() => {
 const reduce=true;

 // Do not let browser scroll restoration reopen the homepage partway down.
 if ('scrollRestoration' in history) history.scrollRestoration='manual';
 const resetInitialViewport=()=>{if(window.scrollY>0 && !sessionStorage.getItem('ffp_home_viewport_ready')){window.scrollTo(0,0);sessionStorage.setItem('ffp_home_viewport_ready','1');}};
 if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',resetInitialViewport,{once:true}); else resetInitialViewport();

 document.querySelectorAll('[data-welcome-profile]').forEach(button=>{
  button.addEventListener('click',()=>{
   const id=button.dataset.welcomeProfile;
   if(window.innerWidth<=650){
    const panel=document.querySelector('[data-welcome-mobile-panel="'+id+'"]');
    if(!panel)return;
    const open=panel.classList.contains('is-open');
    document.querySelectorAll('[data-welcome-mobile-panel]').forEach(p=>{p.classList.remove('is-open');p.hidden=true;});
    document.querySelectorAll('[data-welcome-profile]').forEach(b=>b.classList.remove('is-expanded'));
    if(!open){panel.hidden=false;panel.classList.add('is-open');button.classList.add('is-expanded');}
    return;
   }
   const modal=document.querySelector('[data-welcome-modal="'+id+'"]');
   if(!modal)return;
   modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';
   modal.querySelector('[data-close-welcome-profile]')?.focus();
  });
 });
 document.querySelectorAll('[data-welcome-mobile-panel]').forEach(panel=>{
  panel.querySelector('[data-close-welcome-mobile]')?.addEventListener('click',e=>{
   e.stopPropagation();
   panel.classList.remove('is-open');panel.hidden=true;
   document.querySelector('[data-welcome-profile="'+panel.dataset.welcomeMobilePanel+'"]')?.classList.remove('is-expanded');
  });
 });
 document.querySelectorAll('[data-welcome-modal]').forEach(modal=>{
  const close=()=>{modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');document.body.style.overflow='';};
  modal.querySelector('[data-close-welcome-profile]')?.addEventListener('click',close);
  modal.addEventListener('click',e=>{if(e.target===modal)close();});
 });

 document.querySelectorAll('[data-welcome]').forEach(section => {
  const button=section.querySelector('.welcome-more-toggle'), more=section.querySelector('.welcome-more-content');
  if(button&&more) button.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',String(!open));more.hidden=open;button.querySelector('span').textContent=open?'Read more':'Show less';});
 });
 const sections=[...document.querySelectorAll('.home-section')];
 if(!sections.length)return;
 // Lightweight scroll choreography: transform-only, one RAF per frame.
 const motionTargets=[...document.querySelectorAll('.welcome-heading,.welcome-copy,.home-section .head>div,.home-section .head>p')];
 const welcomeProfiles=[...document.querySelectorAll('.welcome-with-team .welcome-profile')];
 let ticking=false;
 const paintMotion=()=>{
  ticking=false;
  if(reduce)return;
  const vh=window.innerHeight||1;
  motionTargets.forEach((el,i)=>{
   const r=el.getBoundingClientRect();
   const center=(r.top+r.height/2-vh/2)/vh;
   const amount=Math.max(-1,Math.min(1,center));
   const direction=(i%2===0?1:-1);
   const mobile=window.innerWidth<=650;
   const maxX=mobile?Math.min(10,window.innerWidth*.025):Math.min(24,window.innerWidth*.018);
   const maxY=mobile?5:8;
   el.style.setProperty('--motion-x',((amount*maxX*direction)).toFixed(2)+'px');
   el.style.setProperty('--motion-y',((amount*maxY*.35)).toFixed(2)+'px');
  });
  if (welcomeProfiles.length && window.innerWidth>650) {
   welcomeProfiles.forEach((el,i)=>{
    const r=el.getBoundingClientRect();
    const center=(r.top+r.height/2-vh/2)/vh;
    const amount=Math.max(-1,Math.min(1,center));
    const x=(amount*Math.min(9,window.innerWidth*.007)*(i%2===0?1:-1));
    el.style.setProperty('--profile-motion-x',x.toFixed(2)+'px');
   });
  }
 };
 const requestMotion=()=>{if(window.matchMedia('(pointer:coarse)').matches || window.innerWidth<=650)return;if(!ticking){ticking=true;requestAnimationFrame(paintMotion)}};
 window.addEventListener('scroll',requestMotion,{passive:true});
 window.addEventListener('resize',requestMotion,{passive:true});
 requestMotion();
 sections.forEach(section=>section.classList.add('reveal-ready')); sections.slice(0,2).forEach(section=>section.classList.add('revealed'));
 if(reduce){sections.forEach(section=>section.classList.add('revealed'));return;}
 const observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('revealed');observer.unobserve(entry.target);}})},{rootMargin:'0px 0px -8% 0px',threshold:.08});
 sections.forEach(section=>observer.observe(section));
})();
</script>
@push('scripts')
<script>
(() => {
    const root = document.querySelector('.home-slider');
    if (!root) return;
    const slides = [...root.querySelectorAll('.slide')];
    if (slides.length < 2) return;

    // Static homepage mode: keep the first slide visible.
    // No autoplay, swipe choreography, or timed transitions.
})();
</script>
@endpush



<style>
/* FINAL HOMEPAGE VISUAL DIRECTION
   Static, clean and executive: no animated/background graphics.
   Uses the shared FuelFree PowerPlant green / blue / silver system. */
.home-v3 .energy-atmosphere,
.home-v3 .welcome:before,
.home-v3 .welcome:after,
.home-v3 .welcome-heading:before,
.home-v3 .cta-card:before,
.home-v3 .cta-card:after,
.home-v3 .home-slider:before{
    display:none !important;
}
.home-v3{
    isolation:isolate;
    color:var(--ff-white);
}
.home-v3 .home-slider{
    margin-top:clamp(18px,3vw,34px);
}
.home-v3 .slide-media{
    border-color:rgba(53,216,106,.20);
    background:var(--ff-surface);
    box-shadow:0 18px 55px rgba(0,0,0,.28);
}
.home-v3 .slide.is-active .slide-media{
    border-color:rgba(53,216,106,.32);
    box-shadow:0 22px 65px rgba(0,0,0,.34),0 0 28px rgba(53,216,106,.055);
}
.home-v3 .eyebrow,
.home-v3 .more,
.home-v3 .read,
.home-v3 .project-top,
.home-v3 .member-role,
.home-v3 .member-more,
.home-v3 .folder-count,
.home-v3 .welcome-more-toggle{
    color:var(--ff-green);
}
.home-v3 .welcome-heading{
    border-bottom-color:rgba(53,216,106,.13);
}
.home-v3 .welcome h1{
    color:var(--ff-white);
}
.home-v3 .welcome h1 em{
    color:#9cefb2;
}
.home-v3 .welcome-rule{
    background:linear-gradient(90deg,var(--ff-green),var(--ff-blue));
}
.home-v3 .stat-card,
.home-v3 .project-card,
.home-v3 .member-card,
.home-v3 .news,
.home-v3 .folder,
.home-v3 .cta-card{
    background:linear-gradient(145deg,rgba(8,26,18,.88),rgba(3,13,9,.94));
    border-color:rgba(53,216,106,.14);
    box-shadow:0 14px 42px rgba(0,0,0,.22);
}
.home-v3 .stat-card:hover,
.home-v3 .project-card:hover,
.home-v3 .member-card:hover,
.home-v3 .news:hover,
.home-v3 .folder:hover{
    border-color:rgba(53,216,106,.28);
    box-shadow:0 20px 52px rgba(0,0,0,.28),0 0 28px rgba(53,216,106,.055);
}
.home-v3 .stat-card i,
.home-v3 .project-top i,
.home-v3 .folder-meta i,
.home-v3 .member-contact i{
    color:var(--ff-green);
}
.home-v3 .project-body>strong{
    color:#9cefb2;
}
.home-v3 .news-kind{
    color:#8ee9a8;
    background:rgba(53,216,106,.055);
}
.home-v3 .news-kind.notice{
    color:#b9c8d1;
    background:rgba(22,141,255,.055);
}
.home-v3 .cta-card{
    background:linear-gradient(135deg,rgba(8,35,21,.96),rgba(4,18,12,.98));
}
.home-v3 .home-section{
    border-top-color:rgba(53,216,106,.08);
}
.home-v3 .empty{
    border-color:rgba(53,216,106,.16);
    color:var(--ff-muted);
    background:rgba(53,216,106,.02);
}
.home-v3 .folder-media,
.home-v3 .project-media,
.home-v3 .member-photo,
.home-v3 .news-media{
    background:#06120d;
}
.home-v3 .welcome-profile{
    background:linear-gradient(145deg,rgba(8,26,18,.90),rgba(3,13,9,.96));
    border-color:rgba(53,216,106,.14);
}
.home-v3 .welcome-profile:hover{
    border-color:rgba(53,216,106,.30);
    box-shadow:0 18px 42px rgba(0,0,0,.24),0 0 28px rgba(53,216,106,.05);
}
.home-v3 .welcome-profile-role,
.home-v3 .welcome-profile-hint{
    color:#8ee9a8;
}
.home-v3 .welcome-profile-photo{
    border-color:rgba(53,216,106,.18);
}
.home-v3 .member-more{
    border-color:rgba(53,216,106,.20);
    background:rgba(53,216,106,.025);
}
.home-v3 .member-more:hover{
    border-color:rgba(53,216,106,.40);
    background:rgba(53,216,106,.07);
}
.home-v3 .home-profile-modal{
    background:rgba(0,5,3,.82);
}
.home-v3 .home-profile-panel{
    background:linear-gradient(145deg,#092014,#04120b 62%,#020906);
    border-color:rgba(53,216,106,.22);
}
@media(max-width:650px){
    .home-v3 .home-slider{margin-top:12px}
    .home-v3 .welcome{padding-top:44px;padding-bottom:42px}
    .home-v3 .section{padding-top:44px;padding-bottom:44px}
}
</style>

<style>
/* === HOMEPAGE PREMIUM HERO SYSTEM ===
   Static editorial composition; no animated background graphics. */
.home-v3 .welcome{
    padding:clamp(58px,7vw,92px) 0 clamp(50px,6vw,76px);
}
.home-v3 .welcome-inner{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(300px,390px);
    align-items:center;
    gap:clamp(34px,5vw,72px);
}
.home-v3 .welcome-content{min-width:0}
.home-v3 .welcome-heading{
    border-bottom:0;
    padding-bottom:20px;
    position:relative;
}
.home-v3 .welcome-heading:after{
    content:"";
    display:block;
    width:min(100%,760px);
    height:1px;
    margin-top:25px;
    background:linear-gradient(90deg,rgba(53,216,106,.48),rgba(22,141,255,.22),transparent);
}
.home-v3 .welcome h1,
.home-v3 .head h2,
.home-v3 .cta-card h2{
    background:linear-gradient(105deg,#f4fff8 0%,#b8ffd0 34%,#35d86a 63%,#168dff 100%);
    -webkit-background-clip:text;
    background-clip:text;
    -webkit-text-fill-color:transparent;
    color:transparent;
    text-shadow:0 0 30px rgba(53,216,106,.06);
}
.home-v3 .welcome h1{
    max-width:980px;
    margin-top:12px;
    font-size:clamp(46px,6vw,86px);
    line-height:.98;
    letter-spacing:-.058em;
}
.home-v3 .welcome-copy{
    max-width:900px;
    padding-top:7px;
}
.home-v3 .welcome-preview{
    color:#a8bdb2;
    font-size:clamp(13px,1.1vw,15px);
    line-height:1.9;
}
.home-v3 .welcome-more-content{
    color:#a8bdb2;
}
.home-v3 .welcome-more-toggle{
    margin-top:20px;
    border-color:rgba(53,216,106,.24);
    background:rgba(53,216,106,.045);
    box-shadow:0 8px 24px rgba(0,0,0,.14);
}
.home-v3 .section .head{
    padding-bottom:17px;
    border-bottom:1px solid rgba(53,216,106,.08);
}
.home-v3 .head h2{
    margin-top:7px;
}
.home-v3 .head p{
    color:#91a99d;
}
.home-v3 .more{
    padding:8px 11px;
    border:1px solid rgba(53,216,106,.18);
    border-radius:999px;
    background:rgba(53,216,106,.025);
}
.home-v3 .cta-card h2{
    max-width:760px;
}
@media(max-width:900px){
    .home-v3 .welcome-inner{
        grid-template-columns:1fr;
        gap:28px;
    }
}
@media(max-width:650px){
    .home-v3 .welcome{
        padding:44px 0 42px;
    }
    .home-v3 .welcome-inner{
        gap:24px;
    }
    .home-v3 .welcome-heading{
        padding-bottom:4px;
    }
    .home-v3 .welcome-heading:after{
        margin-top:19px;
    }
    .home-v3 .welcome h1{
        font-size:clamp(38px,11vw,52px);
        line-height:1;
        letter-spacing:-.05em;
    }
    .home-v3 .welcome-preview{
        font-size:13px;
        line-height:1.78;
    }
    .home-v3 .welcome-copy{
        padding-top:3px;
    }
    .home-v3 .welcome-vision-title{
        font-size:22px;
    }
    .home-v3 .welcome-vision-copy{
        font-size:10px;
    }
    .home-v3 .welcome-vision-list div{
        font-size:9px;
    }
    .home-v3 .section .head{
        padding-bottom:13px;
    }
}
@media(prefers-reduced-motion:reduce){
    .home-v3 .stat-card,
    .home-v3 .project-card,
    .home-v3 .member-card,
    .home-v3 .news,
    .home-v3 .folder{
        transition:none!important;
    }
}
</style>




<style>
/* FINAL: Homepage Board of Directors desktop card system */
@media (min-width: 992px){
    .home-section-management .management-grid{
        display:grid !important;
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        gap:24px !important;
        width:100% !important;
        max-width:none !important;
        align-items:stretch !important;
    }
    .home-section-management .member-card{
        position:relative !important;
        display:grid !important;
        grid-template-columns:minmax(0,38%) minmax(0,62%) !important;
        grid-template-rows:1fr !important;
        width:100% !important;
        min-width:0 !important;
        min-height:420px !important;
        height:auto !important;
        aspect-ratio:auto !important;
        align-self:stretch !important;
        overflow:hidden !important;
        isolation:isolate;
        border:1px solid transparent !important;
        border-radius:22px !important;
        background:
            linear-gradient(145deg,rgba(8,28,18,.98),rgba(3,13,9,.99)) padding-box,
            linear-gradient(115deg,rgba(53,216,106,.78),rgba(53,216,106,.24) 44%,rgba(22,141,255,.72)) border-box !important;
        box-shadow:0 18px 55px rgba(0,0,0,.32),0 0 28px rgba(53,216,106,.045) !important;
        transition:transform .25s cubic-bezier(.2,.75,.2,1),box-shadow .25s ease !important;
    }
    .home-section-management .member-card::before{
        content:"";
        position:absolute;
        inset:0;
        z-index:0;
        pointer-events:none;
        border-radius:inherit;
        background:
            radial-gradient(circle at 100% 0%,rgba(22,141,255,.09),transparent 35%),
            radial-gradient(circle at 0% 100%,rgba(53,216,106,.08),transparent 38%);
    }
    .home-section-management .member-card::after{
        content:attr(data-card-number);
        position:absolute;
        top:20px;
        right:20px;
        z-index:1;
        color:rgba(53,216,106,.07);
        font-size:62px;
        line-height:1;
        font-weight:900;
        letter-spacing:-.06em;
        pointer-events:none;
    }
    .home-section-management .member-card:hover{
        transform:translateY(-4px) !important;
        box-shadow:0 28px 72px rgba(0,0,0,.42),0 0 34px rgba(53,216,106,.10) !important;
    }
    .home-section-management .member-photo{
        position:relative !important;
        z-index:1;
        grid-column:1 !important;
        grid-row:1 !important;
        width:100% !important;
        height:100% !important;
        min-height:420px !important;
        aspect-ratio:auto !important;
        overflow:hidden !important;
        background:#06120d !important;
        border:0 !important;
    }
    .home-section-management .member-photo::after{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        background:linear-gradient(90deg,transparent 68%,rgba(3,13,9,.38) 100%);
    }
    .home-section-management .member-photo img{
        display:block !important;
        width:100% !important;
        height:100% !important;
        object-fit:cover !important;
        object-position:center !important;
        transition:transform .55s cubic-bezier(.2,.75,.2,1),filter .35s ease !important;
    }
    .home-section-management .member-card:hover .member-photo img{
        transform:scale(1.025);
    }
    .home-section-management .member-body{
        position:relative !important;
        z-index:2;
        grid-column:2 !important;
        grid-row:1 !important;
        width:auto !important;
        height:100% !important;
        min-width:0 !important;
        min-height:0 !important;
        display:flex !important;
        flex-direction:column !important;
        justify-content:center !important;
        align-self:stretch !important;
        padding:38px 34px 30px !important;
        overflow:hidden !important;
        background:transparent !important;
    }
    .home-section-management .member-body::before{
        content:"";
        position:absolute;
        left:0;
        top:13%;
        bottom:13%;
        width:1px;
        background:linear-gradient(180deg,transparent,rgba(53,216,106,.45),rgba(22,141,255,.30),transparent);
    }
    .home-section-management .member-body h3{
        max-width:88% !important;
        margin:0 !important;
        color:transparent !important;
        background:linear-gradient(105deg,#35d86a 0%,#8cf1a8 40%,#33bfff 100%) !important;
        -webkit-background-clip:text !important;
        background-clip:text !important;
        -webkit-text-fill-color:transparent !important;
        font-size:clamp(25px,2.1vw,34px) !important;
        line-height:1.12 !important;
        font-weight:850 !important;
        letter-spacing:-.035em !important;
    }
    .home-section-management .member-role{
        margin:10px 0 0 !important;
        color:#55e58a !important;
        font-size:11px !important;
        line-height:1.4 !important;
        font-weight:850 !important;
        letter-spacing:.19em !important;
        text-transform:uppercase !important;
    }
    .home-section-management .member-contacts{
        display:grid !important;
        gap:10px !important;
        margin:20px 0 0 !important;
        padding:16px 0 !important;
        border-top:1px solid rgba(53,216,106,.12) !important;
        border-bottom:1px solid rgba(53,216,106,.08) !important;
    }
    .home-section-management .member-contact{
        display:flex !important;
        align-items:center !important;
        gap:9px !important;
        min-width:0 !important;
        color:#b7c9c0 !important;
        font-size:12px !important;
        line-height:1.45 !important;
    }
    .home-section-management .member-contact i{
        width:28px !important;
        height:28px !important;
        flex:0 0 28px !important;
        display:grid !important;
        place-items:center !important;
        border-radius:8px !important;
        background:rgba(53,216,106,.06) !important;
        border:1px solid rgba(53,216,106,.13) !important;
        color:#35d86a !important;
        font-size:11px !important;
    }
    .home-section-management .member-message{
        margin:20px 0 0 !important;
        padding:0 !important;
        border:0 !important;
        color:#a5b9ae !important;
        font-size:13px !important;
        line-height:1.78 !important;
        font-weight:400 !important;
        -webkit-line-clamp:3 !important;
        line-clamp:3 !important;
        overflow:hidden !important;
    }
    .home-section-management .member-message::before{
        content:none !important;
        display:none !important;
    }
    .home-section-management .member-more{
        display:inline-flex !important;
        width:auto !important;
        min-width:235px !important;
        min-height:46px !important;
        align-self:flex-start !important;
        margin:20px 0 0 !important;
        padding:10px 17px !important;
        align-items:center !important;
        justify-content:center !important;
        gap:9px !important;
        border:1px solid rgba(53,216,106,.28) !important;
        border-radius:12px !important;
        background:linear-gradient(135deg,rgba(53,216,106,.075),rgba(22,141,255,.035)) !important;
        color:#dff8e7 !important;
        font-size:11px !important;
        font-weight:800 !important;
        line-height:1.2 !important;
        transition:transform .2s ease,border-color .2s ease,background .2s ease,box-shadow .2s ease !important;
    }
    .home-section-management .member-more:hover{
        transform:translateY(-2px) !important;
        border-color:rgba(53,216,106,.50) !important;
        background:linear-gradient(135deg,rgba(53,216,106,.15),rgba(22,141,255,.065)) !important;
        box-shadow:0 10px 25px rgba(0,0,0,.24),0 0 18px rgba(53,216,106,.06) !important;
    }
}
</style>




<style>
/* FINAL DESKTOP MESSAGE VIEWER — executive profile presentation */
.home-profile-modal{position:fixed;inset:0;z-index:9998;display:none;align-items:center;justify-content:center;padding:28px;background:rgba(0,5,8,.86);backdrop-filter:blur(18px) saturate(120%)}
.home-profile-modal.open{display:flex}
.home-profile-panel{position:relative;width:min(1120px,92vw);height:min(720px,86vh);display:grid;grid-template-columns:minmax(260px,34%) minmax(0,66%);overflow:hidden;border:1px solid transparent;border-radius:28px;background:linear-gradient(#04140d,#04140d) padding-box,linear-gradient(135deg,rgba(55,224,108,.55),rgba(35,183,255,.52)) border-box;box-shadow:0 45px 130px rgba(0,0,0,.72),0 0 90px rgba(30,206,137,.10)}
.home-profile-identity{min-width:0;padding:34px 28px 30px;display:flex;flex-direction:column;align-items:center;background:radial-gradient(380px 360px at 50% 5%,rgba(44,214,117,.09),transparent 72%),linear-gradient(180deg,rgba(8,37,23,.72),rgba(2,15,9,.96));border-right:1px solid rgba(74,218,129,.14)}
.home-profile-kicker{align-self:flex-start;margin-bottom:20px;color:#58e58d;font-size:9px;font-weight:800;letter-spacing:.22em;text-transform:uppercase}
.home-profile-photo{width:min(210px,82%);aspect-ratio:4/5;display:grid;place-items:center;overflow:hidden;border-radius:16px;background:#06150d;border:1px solid rgba(64,220,121,.42);box-shadow:0 0 0 4px rgba(64,220,121,.035),0 22px 55px rgba(0,0,0,.38)}
.home-profile-photo img{width:100%;height:100%;display:block;object-fit:cover;object-position:center top}
.home-profile-photo-placeholder{display:grid;place-items:center;width:100%;height:100%;color:#58e58d;font-size:48px}
.home-profile-identity-copy{width:100%;margin-top:20px;text-align:center}
.home-profile-title{margin:0;color:#f1f8f3;font-size:clamp(23px,2vw,30px);line-height:1.15;letter-spacing:-.035em}
.home-profile-role{margin-top:8px;color:#65e895;font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}
.home-profile-contacts{width:100%;display:grid;gap:8px;margin-top:22px;padding-top:18px;border-top:1px solid rgba(74,218,129,.13)}
.home-profile-contact{display:grid;grid-template-columns:34px minmax(0,1fr);align-items:center;gap:9px;min-width:0;color:#b9d1c1;text-decoration:none}
.home-profile-contact>i{width:34px;height:34px;display:grid;place-items:center;border:1px solid rgba(74,218,129,.15);border-radius:10px;background:rgba(74,218,129,.045);color:#63e996;font-size:12px}
.home-profile-contact span{min-width:0;display:grid;gap:2px}.home-profile-contact small{color:#5ed98a;font-size:7px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.home-profile-contact strong{overflow-wrap:anywhere;color:#cfe2d5;font-size:10px;line-height:1.45;font-weight:600}
.home-profile-message-pane{position:relative;min-width:0;min-height:0;display:flex;flex-direction:column;padding:38px 44px 38px;background:radial-gradient(600px 500px at 100% 100%,rgba(31,157,208,.055),transparent 70%),linear-gradient(145deg,#061b10 0%,#03140b 56%,#020c07 100%)}
.home-profile-close{position:absolute;top:22px;right:22px;z-index:3;width:42px;height:42px;border:1px solid rgba(74,218,129,.18);border-radius:12px;background:rgba(74,218,129,.045);color:#86eba8;cursor:pointer}
.home-profile-close:hover{background:rgba(74,218,129,.10);border-color:rgba(74,218,129,.38)}
.home-profile-message-head{padding-right:58px}.home-profile-section-title{margin:0;color:#63e996;font-size:10px;font-weight:800;letter-spacing:.19em;text-transform:uppercase}.home-profile-message-rule{width:54px;height:3px;margin-top:10px;border-radius:99px;background:linear-gradient(90deg,#55e58a,#29c7ff)}
.home-profile-scroll{min-height:0;flex:1;overflow:auto;margin-top:25px;padding:0 18px 10px 0;scrollbar-width:thin;scrollbar-color:rgba(74,218,129,.25) transparent}.home-profile-scroll::-webkit-scrollbar{width:6px}.home-profile-scroll::-webkit-scrollbar-thumb{background:rgba(74,218,129,.25);border-radius:99px}
.home-profile-message{max-width:780px;color:#b9cfc0;font-size:14px;line-height:1.9}.home-profile-message p{margin:0 0 18px}.home-profile-message p:last-child{margin-bottom:0}.home-profile-message p:first-child{color:#e2eee5;font-size:17px;line-height:1.65}
@media(max-width:900px) and (min-width:651px){.home-profile-panel{width:min(960px,94vw);grid-template-columns:minmax(230px,32%) minmax(0,68%)}.home-profile-identity{padding:28px 20px}.home-profile-photo{width:min(175px,86%)}.home-profile-message-pane{padding:32px 30px}.home-profile-message{font-size:13px}.home-profile-message p:first-child{font-size:15px}}
@media(max-width:650px){
    .home-profile-modal{
        padding:14px !important;
        align-items:center !important;
        justify-content:center !important;
        background:rgba(0,5,9,.90) !important;
        backdrop-filter:blur(18px) saturate(125%) !important;
    }
    .home-profile-panel{
        width:min(100%,430px) !important;
        height:min(94svh,860px) !important;
        max-height:calc(100svh - 28px) !important;
        display:grid !important;
        grid-template-columns:1fr !important;
        grid-template-rows:auto minmax(0,1fr) auto !important;
        overflow:hidden !important;
        border-radius:24px !important;
        border:1px solid transparent !important;
        background:
            linear-gradient(145deg,#041b12 0%,#02120b 62%,#020b07 100%) padding-box,
            linear-gradient(135deg,#35e68a 0%,#14d7c8 45%,#168dff 100%) border-box !important;
        box-shadow:0 34px 100px rgba(0,0,0,.72),0 0 70px rgba(25,208,150,.10) !important;
    }
    .home-profile-identity{
        grid-column:1 !important;
        grid-row:1 !important;
        width:100% !important;
        min-width:0 !important;
        display:grid !important;
        grid-template-columns:116px minmax(0,1fr) !important;
        grid-template-rows:auto auto auto !important;
        column-gap:18px !important;
        align-items:center !important;
        padding:22px 18px 18px !important;
        border-right:0 !important;
        border-bottom:1px solid rgba(67,220,145,.14) !important;
        background:
            radial-gradient(420px 230px at 0% 0%,rgba(45,226,131,.11),transparent 68%),
            linear-gradient(180deg,rgba(7,34,22,.82),rgba(2,15,10,.98)) !important;
    }
    .home-profile-kicker{
        grid-column:1/-1 !important;
        grid-row:1 !important;
        margin:0 0 10px !important;
        color:#62ed9a !important;
        font-size:8px !important;
        letter-spacing:.20em !important;
    }
    .home-profile-photo{
        grid-column:1 !important;
        grid-row:2 / 4 !important;
        width:116px !important;
        height:146px !important;
        justify-self:start !important;
        align-self:center !important;
        border-radius:16px !important;
        border:1px solid rgba(56,232,144,.62) !important;
        box-shadow:0 0 0 4px rgba(56,232,144,.035),0 18px 45px rgba(0,0,0,.38) !important;
    }
    .home-profile-identity-copy{
        grid-column:2 !important;
        grid-row:2 !important;
        width:100% !important;
        min-width:0 !important;
        margin:0 !important;
        align-self:end !important;
        text-align:left !important;
    }
    .home-profile-title{
        margin:0 !important;
        color:#f4fbf7 !important;
        font-size:clamp(20px,5.7vw,27px) !important;
        line-height:1.10 !important;
        letter-spacing:-.035em !important;
        overflow-wrap:anywhere !important;
    }
    .home-profile-role{
        margin-top:7px !important;
        color:#31e9b1 !important;
        font-size:9px !important;
        font-weight:900 !important;
        letter-spacing:.15em !important;
    }
    .home-profile-contacts{
        grid-column:2 !important;
        grid-row:3 !important;
        width:100% !important;
        display:grid !important;
        grid-template-columns:1fr !important;
        gap:7px !important;
        margin:10px 0 0 !important;
        padding:10px 0 0 !important;
        border-top:1px solid rgba(65,220,142,.15) !important;
        align-self:start !important;
    }
    .home-profile-contact{
        display:grid !important;
        grid-template-columns:31px minmax(0,1fr) !important;
        gap:8px !important;
        align-items:center !important;
        min-width:0 !important;
    }
    .home-profile-contact>i{
        width:31px !important;
        height:31px !important;
        border-radius:9px !important;
        background:linear-gradient(145deg,rgba(44,220,120,.18),rgba(19,119,93,.12)) !important;
        border:1px solid rgba(54,222,139,.20) !important;
        color:#e7fff1 !important;
        font-size:11px !important;
    }
    .home-profile-contact small{
        color:#55e994 !important;
        font-size:6px !important;
        letter-spacing:.15em !important;
    }
    .home-profile-contact strong{
        color:#d5e9dc !important;
        font-size:8.5px !important;
        line-height:1.3 !important;
        white-space:nowrap !important;
        overflow:hidden !important;
        text-overflow:ellipsis !important;
    }
    .home-profile-message-pane{
        grid-column:1 !important;
        grid-row:2 !important;
        min-width:0 !important;
        min-height:0 !important;
        width:100% !important;
        display:flex !important;
        flex-direction:column !important;
        padding:22px 18px 12px !important;
        overflow:hidden !important;
        background:
            radial-gradient(500px 360px at 100% 100%,rgba(17,130,255,.055),transparent 70%),
            linear-gradient(145deg,#061b11 0%,#03140b 58%,#020c07 100%) !important;
        border-top:1px solid rgba(25,192,163,.08) !important;
    }
    .home-profile-close{
        top:14px !important;
        right:14px !important;
        width:40px !important;
        height:40px !important;
        border-radius:12px !important;
        border-color:rgba(43,177,255,.42) !important;
        background:rgba(9,54,76,.34) !important;
        color:#f2fbff !important;
        font-size:16px !important;
    }
    .home-profile-message-head{
        padding-right:54px !important;
    }
    .home-profile-section-title{
        color:#22edbd !important;
        font-size:9px !important;
        letter-spacing:.19em !important;
    }
    .home-profile-message-rule{
        width:58px !important;
        height:4px !important;
        margin-top:10px !important;
        background:linear-gradient(90deg,#2de889,#18cfff) !important;
    }
    .home-profile-scroll{
        flex:1 1 auto !important;
        min-height:0 !important;
        margin-top:24px !important;
        padding:0 12px 14px 0 !important;
        overflow-y:auto !important;
        overflow-x:hidden !important;
        -webkit-overflow-scrolling:touch !important;
        touch-action:pan-y !important;
        scrollbar-width:thin !important;
        scrollbar-color:#28dfc0 transparent !important;
    }
    .home-profile-message{
        width:100% !important;
        max-width:none !important;
        color:#b9d1e0 !important;
        font-size:12px !important;
        line-height:1.78 !important;
    }
    .home-profile-message p{
        margin:0 0 18px !important;
    }
    .home-profile-message p:first-child{
        color:#f0f7fb !important;
        font-size:16px !important;
        line-height:1.48 !important;
        font-weight:750 !important;
    }
    .home-profile-add-contact{
        grid-column:1 !important;
        grid-row:3 !important;
        width:calc(100% - 36px) !important;
        min-height:58px !important;
        margin:0 18px 16px !important;
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
        gap:11px !important;
        border:1px solid transparent !important;
        border-radius:15px !important;
        background:
            linear-gradient(120deg,rgba(21,119,84,.45),rgba(7,73,108,.42)) padding-box,
            linear-gradient(105deg,#2eea8b,#12d6cb 48%,#168dff) border-box !important;
        color:#f4fff8 !important;
        text-decoration:none !important;
        font-size:15px !important;
        font-weight:850 !important;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.07),0 12px 32px rgba(0,0,0,.30) !important;
    }
    .home-profile-add-contact i{
        color:#27e6f1 !important;
        font-size:20px !important;
    }
}
@media(max-width:420px){
    .home-profile-modal{padding:10px !important}
    .home-profile-panel{
        height:min(95svh,820px) !important;
        max-height:calc(100svh - 20px) !important;
        border-radius:20px !important;
    }
    .home-profile-identity{
        grid-template-columns:94px minmax(0,1fr) !important;
        column-gap:13px !important;
        padding:18px 14px 15px !important;
    }
    .home-profile-photo{
        width:94px !important;
        height:119px !important;
    }
    .home-profile-title{font-size:18px !important}
    .home-profile-message-pane{padding:18px 14px 10px !important}
    .home-profile-message{font-size:11px !important}
    .home-profile-message p:first-child{font-size:14px !important}
    .home-profile-add-contact{
        width:calc(100% - 28px) !important;
        margin-left:14px !important;
        margin-right:14px !important;
        margin-bottom:12px !important;
        min-height:52px !important;
        font-size:13px !important;
    }
}
</style>

@endsection