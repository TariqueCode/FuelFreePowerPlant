@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
@endphp

@section('title', $siteName)

@section('content')
<style>
:root{--bg:#031018;--panel:#071b26;--line:rgba(83,218,240,.16);--text:#effcff;--muted:#91aeb8;--cyan:#48d8f1;--max:1280px}
*{box-sizing:border-box}
html{overflow-x:hidden}
body{margin:0;overflow-x:hidden;background:radial-gradient(circle at 10% 0,rgba(33,187,221,.13),transparent 30%),linear-gradient(180deg,#020a10,#061721 55%,#020a10);color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
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
.welcome-layout-center .welcome-heading,.welcome-layout-center .welcome-copy{text-align:center;margin-inline:auto}
.welcome-layout-right .welcome-heading,.welcome-layout-right .welcome-copy{margin-left:auto;text-align:right}
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
.shell{width:calc(100% - 18px)}.welcome{padding-top:41px}.welcome h1{font-size:29px}.welcome-preview,.welcome-more-content{font-size:13px}.stats-grid,.project-grid,.management-grid{grid-template-columns:1fr}.project-media{aspect-ratio:16/10}.news{grid-template-columns:94px 1fr;min-height:94px}.news-media{width:94px;height:94px}.folders{grid-template-columns:1fr}.head h2{font-size:23px}
}
@media(prefers-reduced-motion:reduce){.slide,.welcome:before,.welcome:after,.section,.project-card,.member-card,.folder,.news{animation:none;transition:none}.slide.is-active{transform:none}}

/* Homepage motion + responsive polish */
.home-slider{isolation:isolate;animation:homeRise .8s cubic-bezier(.2,.7,.2,1) both}
.home-slider:before{content:"";position:absolute;inset:-18px -10px auto;height:70%;z-index:-1;border-radius:40px;background:radial-gradient(circle at 50% 35%,rgba(72,216,241,.16),transparent 62%);filter:blur(22px);animation:heroGlow 7s ease-in-out infinite}
.slide-media{box-shadow:0 20px 70px rgba(0,0,0,.28);transition:border-color .35s ease,box-shadow .35s ease,transform .35s ease}
.slide.is-active .slide-media{border-color:rgba(72,216,241,.34);box-shadow:0 24px 80px rgba(0,0,0,.34),0 0 50px rgba(72,216,241,.08)}
.slide.is-active .slide-media:after{content:"";position:absolute;inset:0;background:linear-gradient(105deg,transparent 25%,rgba(255,255,255,.09) 48%,transparent 58%);transform:translateX(-120%);animation:heroSweep 6s ease-in-out infinite;pointer-events:none}
.home-section{position:relative;overflow:visible}
.home-section:not(.cta-section):after{content:"";position:absolute;left:8%;right:8%;bottom:0;height:1px;background:linear-gradient(90deg,transparent,rgba(72,216,241,.11),transparent);pointer-events:none}
.section{animation:none;opacity:1;transform:none}
.home-section.reveal-ready{opacity:0;transform:translateY(24px) scale(.992);transition:opacity .75s cubic-bezier(.2,.7,.2,1),transform .75s cubic-bezier(.2,.7,.2,1)}
.home-section.revealed{opacity:1;transform:none}
.home-section .head,.welcome-heading,.welcome-copy{position:relative}
.home-section .head:after{content:"";position:absolute;right:0;bottom:-10px;width:44px;height:1px;background:var(--cyan);opacity:.35;transform-origin:right;transform:scaleX(0);transition:transform .8s ease .15s}
.home-section.revealed .head:after{transform:scaleX(1)}
.stat-card,.project-card,.member-card,.news,.folder{position:relative;transform:translateY(0);transition:transform .35s cubic-bezier(.2,.7,.2,1),border-color .35s ease,box-shadow .35s ease,background .35s ease}
.stat-card:before,.project-card:before,.member-card:before,.news:before,.folder:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(120deg,rgba(72,216,241,.08),transparent 35%,transparent 70%,rgba(72,216,241,.035));opacity:0;transition:opacity .35s ease;pointer-events:none}
.stat-card:hover:before,.project-card:hover:before,.member-card:hover:before,.news:hover:before,.folder:hover:before{opacity:1}
.home-section.revealed .stat-card,.home-section.revealed .project-card,.home-section.revealed .member-card,.home-section.revealed .news,.home-section.revealed .folder{animation:cardIn .6s cubic-bezier(.2,.7,.2,1) both}
.home-section.revealed .stat-card:nth-child(2),.home-section.revealed .project-card:nth-child(2),.home-section.revealed .member-card:nth-child(2),.home-section.revealed .news:nth-child(2),.home-section.revealed .folder:nth-child(2){animation-delay:.07s}
.home-section.revealed .stat-card:nth-child(3),.home-section.revealed .project-card:nth-child(3),.home-section.revealed .member-card:nth-child(3),.home-section.revealed .news:nth-child(3),.home-section.revealed .folder:nth-child(3){animation-delay:.14s}
.home-section.revealed .stat-card:nth-child(4),.home-section.revealed .project-card:nth-child(4),.home-section.revealed .member-card:nth-child(4),.home-section.revealed .news:nth-child(4),.home-section.revealed .folder:nth-child(4){animation-delay:.21s}
.stat-card i,.project-top i,.folder-meta i{transition:transform .35s ease}.stat-card:hover i,.project-card:hover .project-top i,.folder:hover .folder-meta i{transform:translateY(-2px) scale(1.08)}
.welcome{overflow:hidden}.welcome-heading:before{content:"";position:absolute;right:2%;top:-10px;width:110px;height:110px;border:1px solid rgba(72,216,241,.08);border-radius:50%;box-shadow:0 0 45px rgba(72,216,241,.06);animation:orbitPulse 5s ease-in-out infinite}.welcome-layout-center .welcome-heading:before{right:12%}.welcome-layout-right .welcome-heading:before{right:auto;left:2%}
.cta-section{border-top-color:rgba(72,216,241,.14)}.cta-card{position:relative;overflow:hidden}.cta-card:before,.cta-card:after{content:"";position:absolute;border-radius:50%;pointer-events:none}.cta-card:before{width:240px;height:240px;right:-100px;top:-150px;border:1px solid rgba(72,216,241,.16);box-shadow:0 0 55px rgba(72,216,241,.08);animation:ctaFloat 8s ease-in-out infinite}.cta-card:after{width:8px;height:8px;right:26%;top:26%;background:var(--cyan);box-shadow:0 0 24px rgba(72,216,241,.55);animation:dotDrift 5s ease-in-out infinite}
@keyframes homeRise{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}@keyframes heroGlow{0%,100%{opacity:.55;transform:scale(.96)}50%{opacity:1;transform:scale(1.03)}}@keyframes heroSweep{0%,55%{transform:translateX(-120%)}75%,100%{transform:translateX(120%)}}@keyframes cardIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}@keyframes orbitPulse{0%,100%{transform:translate3d(0,0,0) scale(1);opacity:.45}50%{transform:translate3d(-8px,10px,0) scale(1.08);opacity:.9}}@keyframes ctaFloat{0%,100%{transform:translate(0,0)}50%{transform:translate(-12px,10px)}}@keyframes dotDrift{0%,100%{transform:translate(0,0);opacity:.45}50%{transform:translate(16px,-12px);opacity:1}}
@media(max-width:900px){.home-section .head:after{display:none}.home-section:not(.cta-section):after{left:4%;right:4%}.welcome-heading:before{width:80px;height:80px;right:0}.cta-card:before{width:170px;height:170px;right:-80px;top:-100px}}
@media(max-width:650px){.home-slider:before{inset:-8px 0 auto;height:90%;filter:blur(16px)}.slide.is-active .slide-media{box-shadow:0 16px 42px rgba(0,0,0,.3),0 0 30px rgba(72,216,241,.06)}.home-section.reveal-ready{transform:translateY(16px)}.welcome-heading:before{width:58px;height:58px;top:0}.home-section.revealed .stat-card,.home-section.revealed .project-card,.home-section.revealed .member-card,.home-section.revealed .news,.home-section.revealed .folder{animation-duration:.45s}.cta-card:after{right:18%;top:22%}}
@media(prefers-reduced-motion:reduce){.home-slider,.home-slider:before,.slide.is-active .slide-media:after,.welcome-heading:before,.cta-card:before,.cta-card:after{animation:none}.home-section.reveal-ready{opacity:1;transform:none;transition:none}.home-section.revealed .stat-card,.home-section.revealed .project-card,.home-section.revealed .member-card,.home-section.revealed .news,.home-section.revealed .folder{animation:none}}

/* === FFP modern motion architecture: GPU-light, responsive, content-safe === */
main.shell{position:relative;isolation:isolate}
main.shell>.energy-atmosphere{position:fixed;inset:0;z-index:-2;pointer-events:none;overflow:hidden;contain:strict;background:
 radial-gradient(circle at 18% 18%,rgba(43,203,232,.08),transparent 28%),
 radial-gradient(circle at 82% 46%,rgba(36,157,191,.06),transparent 25%),
 linear-gradient(180deg,rgba(2,11,17,.15),rgba(1,8,13,.32))}
.energy-atmosphere .energy-grid{position:absolute;inset:-20%;opacity:.17;background-image:
 linear-gradient(rgba(72,216,241,.08) 1px,transparent 1px),
 linear-gradient(90deg,rgba(72,216,241,.08) 1px,transparent 1px);
 background-size:72px 72px;transform:perspective(700px) rotateX(58deg) translateY(18%);transform-origin:center bottom;
 mask-image:linear-gradient(to top,black,transparent 72%);animation:gridDrift 22s linear infinite}
.energy-atmosphere .energy-core{position:absolute;width:min(42vw,520px);aspect-ratio:1;left:50%;top:47%;border:1px solid rgba(72,216,241,.08);border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 0 90px rgba(72,216,241,.035),inset 0 0 70px rgba(72,216,241,.025);animation:coreBreath 10s ease-in-out infinite}
.energy-atmosphere .energy-core:before,.energy-atmosphere .energy-core:after{content:"";position:absolute;inset:12%;border:1px solid rgba(72,216,241,.06);border-radius:50%}
.energy-atmosphere .energy-core:after{inset:27%;border-style:dashed;animation:coreSpin 28s linear infinite}
.energy-atmosphere .energy-orbit{position:absolute;width:clamp(260px,34vw,520px);height:clamp(90px,12vw,180px);left:50%;top:48%;border:1px solid rgba(72,216,241,.07);border-radius:50%;transform:translate(-50%,-50%) rotate(-14deg);animation:orbitFloat 13s ease-in-out infinite}
.energy-atmosphere .energy-orbit:after{content:"";position:absolute;width:5px;height:5px;left:17%;top:18%;border-radius:50%;background:var(--cyan);box-shadow:0 0 18px rgba(72,216,241,.65)}
.energy-atmosphere .energy-pulse{position:absolute;left:6%;right:6%;top:58%;height:1px;background:linear-gradient(90deg,transparent,rgba(72,216,241,.08),transparent);animation:pulseTravel 8s ease-in-out infinite}
.energy-atmosphere .energy-pulse:before,.energy-atmosphere .energy-pulse:after{content:"";position:absolute;top:-3px;width:7px;height:7px;border:1px solid rgba(72,216,241,.28);transform:rotate(45deg);background:#031018}
.home-section .head>div,.welcome-heading,.welcome-copy{will-change:transform}
.home-section .head>div{transform:translate3d(var(--motion-x,0px),var(--motion-y,0px),0)}
.welcome-heading{transform:translate3d(var(--motion-x,0px),var(--motion-y,0px),0)}
.welcome-copy{transform:translate3d(calc(var(--motion-x,0px) * -.65),calc(var(--motion-y,0px) * -.65),0)}
.home-section:nth-of-type(odd) .head>div{--motion-x:0px}
.home-section .head>p{transform:translate3d(calc(var(--motion-x,0px) * -.55),0,0);will-change:transform}
@keyframes gridDrift{from{background-position:0 0}to{background-position:0 72px}}
@keyframes coreBreath{0%,100%{opacity:.45;transform:translate(-50%,-50%) scale(.96)}50%{opacity:.8;transform:translate(-50%,-50%) scale(1.035)}}
@keyframes coreSpin{to{transform:rotate(360deg)}}
@keyframes orbitFloat{0%,100%{transform:translate(-50%,-50%) rotate(-14deg) scale(1)}50%{transform:translate(-50%,-50%) rotate(-8deg) scale(1.04)}}
@keyframes pulseTravel{0%,100%{opacity:.25;transform:scaleX(.92)}50%{opacity:.75;transform:scaleX(1)}}

/* Desktop: editorial wide composition */
/* Desktop welcome composition: selected leadership profiles beside the introduction. */
.welcome-with-team .welcome-inner{display:grid;grid-template-columns:minmax(235px,285px) minmax(0,1fr);gap:42px;align-items:center}
.welcome-team{display:grid;gap:14px;align-self:stretch;align-content:center}
.welcome-team-label{color:#69d9ec;font-size:8px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}
.welcome-profile{position:relative;display:grid;grid-template-columns:76px minmax(0,1fr);gap:12px;align-items:center;min-width:0;padding:11px;border:1px solid rgba(72,216,241,.13);border-radius:16px;background:linear-gradient(145deg,rgba(8,37,50,.88),rgba(3,19,27,.92));color:inherit;cursor:pointer;text-align:left;transition:transform .28s cubic-bezier(.2,.7,.2,1),border-color .28s ease,box-shadow .28s ease}
.welcome-profile:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(120deg,rgba(72,216,241,.08),transparent 45%);opacity:0;transition:opacity .28s ease;pointer-events:none}
.welcome-profile:hover{transform:translateX(5px);border-color:rgba(72,216,241,.34);box-shadow:0 14px 34px rgba(0,0,0,.22),0 0 28px rgba(72,216,241,.045)}
.welcome-profile:hover:before{opacity:1}
.welcome-profile-photo{width:76px;height:76px;border-radius:13px;overflow:hidden;background:#061923;display:grid;place-items:center;color:#55d4ed;font-size:25px;border:1px solid rgba(72,216,241,.13)}
.welcome-profile-photo img{width:100%;height:100%;object-fit:cover;display:block}
.welcome-profile-copy{min-width:0}
.welcome-profile-name{margin:0;color:#eaf8fb;font-size:12px;line-height:1.3;font-weight:800;overflow-wrap:anywhere}
.welcome-profile-role{margin-top:4px;color:#63d4e9;font-size:8px;line-height:1.4;font-weight:750}
.welcome-profile-message{margin-top:7px;color:#7f9ea8;font-size:8px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.welcome-profile-hint{margin-top:6px;color:#5bcde4;font-size:7px;font-weight:800;letter-spacing:.04em}
.welcome-with-team .welcome-content{min-width:0}
.welcome-profile-modal{position:fixed;inset:0;z-index:160;display:none;align-items:center;justify-content:center;padding:24px;background:rgba(0,5,9,.78);backdrop-filter:blur(16px) saturate(120%)}
.welcome-profile-modal.is-open{display:flex}
.welcome-profile-dialog{width:min(900px,94vw);max-height:min(720px,88vh);display:grid;grid-template-columns:260px minmax(0,1fr);overflow:hidden;border:1px solid rgba(72,216,241,.24);border-radius:24px;background:radial-gradient(420px 300px at 0 0,rgba(72,216,241,.09),transparent 70%),linear-gradient(145deg,#092936,#041923 62%,#021119);box-shadow:0 36px 110px rgba(0,0,0,.68),0 0 60px rgba(72,216,241,.06);animation:welcomeProfileIn .2s ease-out}
@keyframes welcomeProfileIn{from{opacity:0;transform:translateY(10px) scale(.985)}to{opacity:1;transform:none}}
.welcome-profile-modal-photo{min-height:0;padding:22px;display:flex;align-items:center;justify-content:center;background:radial-gradient(300px 300px at 50% 35%,rgba(72,216,241,.08),transparent 72%),#061923;border-right:1px solid rgba(72,216,241,.12)}
.welcome-profile-modal-photo img{width:auto;height:auto;max-width:100%;max-height:100%;object-fit:contain;border-radius:14px;border:1px solid rgba(72,216,241,.45);box-shadow:0 18px 45px rgba(0,0,0,.35)}
.welcome-profile-modal-photo .fallback{font-size:54px;color:#4fcde9}
.welcome-profile-modal-body{min-width:0;display:flex;flex-direction:column;min-height:0}
.welcome-profile-modal-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:25px 28px 18px}
.welcome-profile-modal-kicker{color:#62d7eb;font-size:8px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}
.welcome-profile-modal-title{margin:6px 0 0;color:#effbfc;font-size:26px;line-height:1.18;letter-spacing:-.03em}
.welcome-profile-modal-role{margin-top:7px;color:#68d8eb;font-size:10px;font-weight:750}
.welcome-profile-modal-close{width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;border:1px solid rgba(72,216,241,.15);border-radius:10px;background:rgba(72,216,241,.05);color:#b9d8df;cursor:pointer}
.welcome-profile-modal-close:hover{background:rgba(72,216,241,.11);color:#fff}
.welcome-profile-modal-divider{height:1px;margin:0 28px;background:rgba(72,216,241,.10)}
.welcome-profile-modal-scroll{min-height:0;overflow:auto;padding:22px 28px 28px;color:#a4bdc6;font-size:12px;line-height:1.9;white-space:normal}
.welcome-profile-modal-scroll p{margin:0 0 14px}.welcome-profile-modal-scroll p:last-child{margin-bottom:0}
.welcome-profile-modal-label{margin:0 0 10px;color:#70ddec;font-size:8px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}
@media(max-width:1099px){.welcome-with-team .welcome-inner{grid-template-columns:minmax(200px,245px) minmax(0,1fr);gap:24px}.welcome-profile{grid-template-columns:62px minmax(0,1fr);padding:9px;gap:9px}.welcome-profile-photo{width:62px;height:62px}}
@media(max-width:650px){.welcome-with-team .welcome-inner{display:block}.welcome-with-team .welcome-content{width:100%}.welcome-with-team .welcome-team{display:grid;margin-top:30px;padding-top:22px;border-top:1px solid rgba(72,216,241,.10);gap:10px}.welcome-with-team .welcome-profile{grid-template-columns:64px minmax(0,1fr);padding:10px;gap:10px}.welcome-with-team .welcome-profile-photo{width:64px;height:64px;border-radius:11px}.welcome-profile-modal{display:none}.welcome-profile-modal.is-open{display:flex}}
@media(prefers-reduced-motion:reduce){.welcome-profile,.welcome-profile-dialog{transition:none;animation:none}}

@media(min-width:1100px){
 main.shell{width:min(1380px,calc(100% - 64px))}
 .home-slider{margin-top:34px}
 .welcome{padding:105px 0 92px}
 .welcome h1{max-width:1120px;font-size:clamp(56px,6.2vw,88px)}
 .welcome-copy{max-width:1050px;padding-top:36px}
 .section{padding:86px 0}
 .head{margin-bottom:30px}
 .stats-grid{gap:18px}.stat-card{padding:27px;border-radius:22px}
 .project-grid,.news-grid{gap:20px}.management-grid,.folders{gap:20px}
 .project-media{height:225px}.cta-card{padding:46px}
}

/* Tablet: compact editorial grid */
@media(min-width:651px) and (max-width:1099px){
 .energy-atmosphere .energy-core{width:58vw}
 .section{padding:64px 0}
 .welcome{padding:76px 0 70px}
}

/* Welcome team composition: message left, selected profiles right on larger screens. */
@media(min-width:651px){
 .welcome-with-team .welcome-inner{
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(280px,360px);
  align-items:start;
  gap:42px;
 }
 .welcome-with-team .welcome-content{grid-column:1;grid-row:1;min-width:0}
 .welcome-with-team .welcome-team{
  grid-column:2;
  grid-row:1;
  align-self:start;
  padding-top:6px;
 }
 .welcome-with-team .welcome-team-label{text-align:left}
 .welcome-with-team .welcome-profile{grid-template-columns:76px minmax(0,1fr);padding:12px;gap:12px}
 .welcome-with-team .welcome-profile-photo{width:76px;height:76px}
 .welcome-with-team .welcome-profile-copy{min-width:0}
}
@media(max-width:650px){
 .welcome-with-team .welcome-inner{display:block}
 .welcome-with-team .welcome-content{width:100%}
 .welcome-with-team .welcome-team{
  display:grid;
  gap:10px;
  margin-top:30px;
  padding-top:22px;
  border-top:1px solid rgba(72,216,241,.10);
 }
 .welcome-with-team .welcome-team-label{margin-bottom:2px}
 .welcome-with-team .welcome-profile{
  grid-template-columns:64px minmax(0,1fr);
  padding:10px;
  gap:10px;
  border-radius:14px;
 }
 .welcome-with-team .welcome-profile-photo{width:64px;height:64px;border-radius:11px}
 .welcome-with-team .welcome-profile-message{-webkit-line-clamp:2}
 .welcome-profile-modal{padding:12px}
 .welcome-profile-dialog{
  width:min(100%,520px);
  max-height:90vh;
  grid-template-columns:1fr;
  border-radius:18px;
 }
 .welcome-profile-modal-photo{
  min-height:170px;
  max-height:220px;
  padding:16px;
  border-right:0;
  border-bottom:1px solid rgba(72,216,241,.12);
 }
 .welcome-profile-modal-photo img{max-height:190px}
 .welcome-profile-modal-head{padding:18px 18px 14px}
 .welcome-profile-modal-title{font-size:21px}
 .welcome-profile-modal-divider{margin:0 18px}
 .welcome-profile-modal-scroll{padding:16px 18px 20px;font-size:11px;line-height:1.8}
}
/* Mobile: deliberately different, vertical-first composition */
@media(max-width:650px){
 main.shell{width:calc(100% - 20px)}
 .energy-atmosphere{opacity:.72}
 .energy-atmosphere .energy-grid{inset:-35%;background-size:42px 42px;transform:perspective(500px) rotateX(64deg) translateY(25%);opacity:.16}
 .energy-atmosphere .energy-core{width:92vw;top:42%;opacity:.72}
 .energy-atmosphere .energy-orbit{width:92vw;height:28vw;top:43%;border-color:rgba(72,216,241,.055)}
 .energy-atmosphere .energy-pulse{left:0;right:0;top:51%}
 .home-slider{margin-top:10px}
 .welcome{padding:54px 0 49px}
 .welcome h1{font-size:clamp(32px,10.8vw,48px);max-width:100%;line-height:.99}
 .welcome-heading{padding-bottom:21px}
 .welcome-copy{padding-top:20px}
 .section{padding:49px 0}
 .head{display:block;margin-bottom:19px}
 .head h2{font-size:clamp(24px,8vw,34px)}
 .head p{max-width:90%;margin-top:9px}
 .more{display:inline-flex;margin-top:12px}
 .stats-grid{grid-template-columns:1fr 1fr}
 .stat-card{min-height:116px}
 .project-grid{grid-template-columns:1fr}
 .project-card{display:grid;grid-template-columns:38% 62%;align-items:stretch}
 .project-media{height:100%;min-height:150px}
 .project-body{display:flex;flex-direction:column;justify-content:center;padding:15px}
 .management-grid{grid-template-columns:1fr 1fr}
 .member-card>div:last-child{min-height:65px}
 .folders{grid-template-columns:1fr 1fr}
 .cta-card{padding:24px 19px}
 .home-section .head>div,.welcome-heading,.welcome-copy{will-change:transform}
 .home-section .head>div{transform:translate3d(var(--motion-x,0px),var(--motion-y,0px),0)}
 .home-section .head>p{transform:none}
}
@media(max-width:380px){
 .stats-grid,.management-grid,.folders{grid-template-columns:1fr}
 .project-card{display:block}.project-media{min-height:0;aspect-ratio:16/9}
 .energy-atmosphere .energy-core{width:115vw}
}
@media(prefers-reduced-motion:reduce){
 .energy-atmosphere *{animation:none!important}
 .home-section .head>div,.home-section .head>p,.welcome-heading,.welcome-copy{transform:none!important;will-change:auto}
}

/* === Final responsive welcome + featured leadership composition === */
@media (min-width:651px){
    .welcome-with-team{
        padding-top:72px;
        padding-bottom:78px;
    }
    .welcome-with-team .welcome-inner{
        display:grid;
        grid-template-columns:minmax(0,1fr) minmax(300px,390px);
        gap:clamp(38px,5vw,78px);
        align-items:center;
    }
    .welcome-with-team .welcome-content{
        grid-column:1;
        grid-row:1;
        min-width:0;
    }
    .welcome-with-team .welcome-heading{
        max-width:100%;
        padding-bottom:30px;
    }
    .welcome-with-team .welcome-heading h1{
        max-width:100%;
        font-size:clamp(48px,5.6vw,78px);
        line-height:.98;
        letter-spacing:-.055em;
    }
    .welcome-with-team .welcome-copy{
        max-width:100%;
        padding-top:27px;
    }
    .welcome-with-team .welcome-preview,
    .welcome-with-team .welcome-more-content{
        max-width:920px;
        font-size:clamp(13px,1.08vw,15px);
        line-height:1.85;
    }

    .welcome-with-team .welcome-team{
        grid-column:2;
        grid-row:1;
        display:grid;
        gap:14px;
        align-self:center;
        align-content:center;
        padding:0;
        min-width:0;
    }
    .welcome-with-team .welcome-team-label{
        margin:0 0 2px 4px;
        color:#67d9ec;
        font-size:9px;
        font-weight:850;
        letter-spacing:.20em;
        text-transform:uppercase;
    }
    .welcome-with-team .welcome-profile{
        width:100%;
        display:grid;
        grid-template-columns:92px minmax(0,1fr);
        gap:15px;
        align-items:center;
        min-height:112px;
        padding:12px;
        border:1px solid rgba(72,216,241,.14);
        border-radius:18px;
        background:
            radial-gradient(180px 120px at 0 0,rgba(72,216,241,.08),transparent 75%),
            linear-gradient(145deg,rgba(8,37,50,.90),rgba(3,19,27,.96));
        box-shadow:0 12px 34px rgba(0,0,0,.16);
        text-align:left;
        transform:translate3d(0,0,0);
        transition:transform .35s cubic-bezier(.2,.7,.2,1),border-color .35s ease,box-shadow .35s ease;
    }
    .welcome-with-team .welcome-profile:hover{
        transform:translate3d(-5px,-2px,0);
        border-color:rgba(72,216,241,.38);
        box-shadow:0 20px 44px rgba(0,0,0,.25),0 0 32px rgba(72,216,241,.06);
    }
    .welcome-with-team .welcome-profile-photo{
        width:92px;
        height:92px;
        border-radius:14px;
        border-color:rgba(72,216,241,.20);
        box-shadow:0 8px 22px rgba(0,0,0,.24);
    }
    .welcome-with-team .welcome-profile-name{
        font-size:13px;
        line-height:1.28;
        letter-spacing:-.01em;
    }
    .welcome-with-team .welcome-profile-role{
        margin-top:5px;
        color:#70dced;
        font-size:8px;
        font-weight:800;
        letter-spacing:.05em;
        text-transform:uppercase;
    }
    .welcome-with-team .welcome-profile-message{
        margin-top:7px;
        color:#91aeb8;
        font-size:8px;
        line-height:1.5;
        -webkit-line-clamp:2;
    }
    .welcome-with-team .welcome-profile-hint{
        margin-top:7px;
        color:#5ed3e9;
        font-size:7px;
        letter-spacing:.05em;
    }

    /* Scroll reveal: content enters from the left, leadership cards from the right. */
    .welcome-with-team.reveal-ready .welcome-content{
        opacity:0;
        transform:translate3d(-34px,14px,0);
        transition:opacity .8s cubic-bezier(.2,.7,.2,1),transform .8s cubic-bezier(.2,.7,.2,1);
    }
    .welcome-with-team.reveal-ready .welcome-profile{
        opacity:0;
        transform:translate3d(34px,18px,0);
        transition:opacity .7s cubic-bezier(.2,.7,.2,1),transform .7s cubic-bezier(.2,.7,.2,1),border-color .35s ease,box-shadow .35s ease;
    }
    .welcome-with-team.revealed .welcome-content{
        opacity:1;
        transform:none;
    }
    .welcome-with-team.revealed .welcome-profile{
        opacity:1;
        transform:translate3d(var(--profile-motion-x,0px),0,0);
    }
    .welcome-with-team.revealed .welcome-profile:nth-child(2){transition-delay:.10s}
    .welcome-with-team.revealed .welcome-profile:nth-child(3){transition-delay:.20s}

    /* Avoid the generic alternating text choreography fighting this composition. */
    .welcome-with-team .welcome-heading,
    .welcome-with-team .welcome-copy{
        will-change:transform;
    }
}

@media (max-width:650px){
    /* Mobile remains vertical-first, but uses a cleaner executive treatment. */
    .welcome-with-team .welcome-team{
        margin-top:34px;
        padding-top:24px;
        gap:11px;
    }
    .welcome-with-team .welcome-team-label{
        color:#67d9ec;
        font-size:8px;
        font-weight:850;
        letter-spacing:.18em;
        text-transform:uppercase;
    }
    .welcome-with-team .welcome-profile{
        grid-template-columns:70px minmax(0,1fr);
        min-height:88px;
        padding:10px;
        gap:11px;
        border-radius:15px;
        background:
            radial-gradient(160px 100px at 0 0,rgba(72,216,241,.07),transparent 75%),
            linear-gradient(145deg,rgba(8,37,50,.90),rgba(3,19,27,.96));
    }
    .welcome-with-team .welcome-profile-photo{
        width:70px;
        height:70px;
        border-radius:12px;
    }
    .welcome-with-team .welcome-profile-name{
        font-size:11px;
        line-height:1.3;
    }
    .welcome-with-team .welcome-profile-role{
        margin-top:4px;
        color:#69d9ec;
        font-size:7px;
        line-height:1.35;
        font-weight:800;
        letter-spacing:.04em;
        text-transform:uppercase;
    }
    .welcome-with-team .welcome-profile-message{
        margin-top:6px;
        font-size:7px;
        line-height:1.45;
        -webkit-line-clamp:2;
    }
    .welcome-with-team .welcome-profile-hint{
        margin-top:5px;
        font-size:6.5px;
    }

    /* Gentle mobile entrance; no heavy parallax or layout-jank. */
    .welcome-with-team.reveal-ready .welcome-content{
        opacity:0;
        transform:translate3d(-18px,10px,0);
        transition:opacity .65s ease,transform .65s ease;
    }
    .welcome-with-team.reveal-ready .welcome-profile{
        opacity:0;
        transform:translate3d(18px,12px,0);
        transition:opacity .55s ease,transform .55s ease;
    }
    .welcome-with-team.revealed .welcome-content,
    .welcome-with-team.revealed .welcome-profile{
        opacity:1;
        transform:none;
    }
    .welcome-with-team.revealed .welcome-profile:nth-child(2){transition-delay:.08s}
    .welcome-with-team.revealed .welcome-profile:nth-child(3){transition-delay:.16s}
}

@media(prefers-reduced-motion:reduce){
    .welcome-with-team.reveal-ready .welcome-content,
    .welcome-with-team.reveal-ready .welcome-profile{
        opacity:1!important;
        transform:none!important;
        transition:none!important;
    }
}

</style>
<main class="shell">
<div class="energy-atmosphere" aria-hidden="true"><span class="energy-grid"></span><span class="energy-core"></span><span class="energy-orbit"></span><span class="energy-pulse"></span></div>
@foreach($home['section_order'] as $section)
@if($section==='hero' && $home['slider'] && $sliders->isNotEmpty())
<section class="home-slider" aria-label="Company highlights">
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
<section class="welcome home-section home-section-welcome welcome-layout-{{ $welcomeLayout }} {{ $welcomeManagement->isNotEmpty() ? 'welcome-with-team' : '' }}" data-welcome>
<div class="welcome-inner">
@if($welcomeManagement->isNotEmpty())
<div class="welcome-team" aria-label="Featured leadership profiles">
<div class="welcome-team-label">Leadership voices</div>
@foreach($welcomeManagement as $member)
@php
$profileMessage=trim(strip_tags((string) $member->content));
$profileShortMessage=\Illuminate\Support\Str::words($profileMessage,20);
@endphp
<button type="button" class="welcome-profile" data-welcome-profile="{{ $member->id }}" aria-label="Open profile of {{ $member->title }}">
<div class="welcome-profile-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user-tie"></i>@endif</div>
<div class="welcome-profile-copy">
<h3 class="welcome-profile-name">{{ $member->title }}</h3>
<div class="welcome-profile-role">{{ $member->designation ?: $member->excerpt }}</div>
@if($profileShortMessage !== '')<div class="welcome-profile-message">{{ $profileShortMessage }}</div>@endif
<div class="welcome-profile-hint">View message <i class="fa-solid fa-arrow-right"></i></div>
</div>
</button>
@endforeach
</div>
@endif
<div class="welcome-content">
<div class="welcome-heading"><span class="eyebrow">{{ $welcomeEyebrow ?: 'Welcome to '.$siteName }}</span><h1>{{ $welcomeTitle ?: 'Building a stronger energy future.' }}</h1><div class="welcome-rule"></div></div>
<div class="welcome-copy">
<div class="welcome-preview">{!! nl2br(e($welcomePreview)) !!}</div>
@if($welcomeHasMore)
<div class="welcome-more-content" hidden>{!! nl2br(e($welcomeRemaining)) !!}</div>
<button type="button" class="welcome-more-toggle" aria-expanded="false"><span>Read more</span><i class="fa-solid fa-arrow-down"></i></button>
@endif
@if($welcomeSignoff !== '')<div class="welcome-signoff">{{ $welcomeSignoff }}</div>@endif
</div>
</div>
</div>
</section>
@endif

@if($section==='statistics' && $home['statistics'])
<section class="section home-section home-section-statistics section-layout-{{ ($sectionSettings['statistics'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Power at a glance</span><h2>Our footprint.</h2></div><p>Key figures are calculated directly from the power plant records managed in the admin portal.</p></div>
<div class="stats-grid">
<div class="stat-card"><i class="fa-solid fa-industry"></i><strong>{{ number_format($stats['projects']) }}</strong><span>Projects</span></div>
<div class="stat-card"><i class="fa-solid fa-bolt"></i><strong>{{ number_format($stats['capacity_mw'],2) }} MW</strong><span>Total capacity</span></div>
<div class="stat-card"><i class="fa-solid fa-circle-check"></i><strong>{{ number_format($stats['operational']) }}</strong><span>Operational plants</span></div>
<div class="stat-card"><i class="fa-solid fa-leaf"></i><strong>Future-ready</strong><span>Energy development</span></div>
</div></section>
@endif

@if($section==='projects' && $home['projects'])
<section class="section home-section home-section-projects section-layout-{{ ($sectionSettings['projects'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Our power plants</span><h2>Projects &amp; plants.</h2></div><a class="more" href="{{ route('site.plants') }}">View all →</a></div>
<div class="project-grid">
@if($plants->isNotEmpty())
@foreach($plants as $plant)
<a class="project-card" href="{{ route('projects.show',$plant->slug) }}">
<div class="project-media">@if($plant->image_path)<img src="{{ asset('storage/'.$plant->image_path) }}" alt="{{ $plant->name }}" loading="lazy">@else<i class="fa-solid fa-industry"></i>@endif</div>
<div class="project-body"><div class="project-top"><span>{{ ucfirst(str_replace('_',' ',$plant->status)) }}</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>{{ $plant->name }}</h3><p>{{ $plant->location ?: $plant->technology ?: 'Power generation project' }}</p><strong>{{ number_format((float)$plant->capacity_kw/1000,2) }} MW</strong></div>
</a>
@endforeach
@else<div class="empty" style="grid-column:1/-1">No power plant projects have been published yet.</div>@endif
</div></section>
@endif

@if($section==='management' && $home['management'])

<section class="section home-section home-section-management section-layout-{{ ($sectionSettings['management'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Leadership</span><h2>Management team.</h2></div><a class="more" href="{{ route('management') }}">Meet the team →</a></div>
<div class="management-grid">
@if($homeManagement->isNotEmpty())
@foreach($homeManagement as $member)
<a class="member-card" href="{{ route('management') }}#member-{{ $member->id }}"><div class="member-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user"></i>@endif</div><div><h3>{{ $member->title }}</h3><p>{{ $member->designation ?: $member->excerpt }}</p></div></a>
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

@if($homeManagement->take(2)->isNotEmpty())
@foreach($homeManagement->take(2) as $member)
@php($profileMessage=trim(strip_tags((string) $member->content)))
<div class="welcome-profile-modal" data-welcome-modal="{{ $member->id }}" aria-hidden="true">
<div class="welcome-profile-dialog" role="dialog" aria-modal="true" aria-labelledby="welcome-profile-title-{{ $member->id }}">
<div class="welcome-profile-modal-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}">@else<div class="fallback"><i class="fa-solid fa-user-tie"></i></div>@endif</div>
<div class="welcome-profile-modal-body">
<div class="welcome-profile-modal-head"><div><div class="welcome-profile-modal-kicker">Management Profile</div><h2 class="welcome-profile-modal-title" id="welcome-profile-title-{{ $member->id }}">{{ $member->title }}</h2><div class="welcome-profile-modal-role">{{ $member->designation ?: $member->excerpt }}</div></div><button type="button" class="welcome-profile-modal-close" data-close-welcome-profile aria-label="Close profile"><i class="fa-solid fa-xmark"></i></button></div>
<div class="welcome-profile-modal-divider"></div>
<div class="welcome-profile-modal-scroll"><div class="welcome-profile-modal-label">Message</div>@if($profileMessage !== ''){!! nl2br(e($profileMessage)) !!}@else<p>No additional profile message is available.</p>@endif</div>
</div>
</div>
</div>
@endforeach
@endif

<script>
(() => {
 const reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;

 document.querySelectorAll('[data-welcome-profile]').forEach(button=>{
  button.addEventListener('click',()=>{
   
   const modal=document.querySelector('[data-welcome-modal="'+button.dataset.welcomeProfile+'"]');
   if(!modal)return;
   modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';
   modal.querySelector('[data-close-welcome-profile]')?.focus();
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
 const requestMotion=()=>{if(!ticking){ticking=true;requestAnimationFrame(paintMotion)}};
 window.addEventListener('scroll',requestMotion,{passive:true});
 window.addEventListener('resize',requestMotion,{passive:true});
 requestMotion();
 sections.forEach(section=>section.classList.add('reveal-ready'));
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

    let index = 0;
    let timer;

    const show = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;
        slides.forEach((slide, i) => slide.classList.toggle('is-active', i === index));
    };

    const start = () => {
        clearInterval(timer);
        timer = setInterval(() => show(index + 1), 5000);
    };

    const pause = () => clearInterval(timer);

    root.addEventListener('mouseenter', pause);
    root.addEventListener('mouseleave', start);
    root.addEventListener('touchstart', pause, {passive:true});
    root.addEventListener('touchend', start, {passive:true});
    root.addEventListener('touchcancel', start, {passive:true});

    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) start();
})();
</script>
@endpush
@endsection
