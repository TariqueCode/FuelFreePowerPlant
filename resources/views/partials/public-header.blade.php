@php
    $publicBrand = $brand ?? [];
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    $publicNavPages = \App\Models\SiteContentItem::query()->where('type','company')->where('status','published')->where('show_in_navigation',true)->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')->orderBy('navigation_order')->orderByDesc('created_at')->get(['title','slug']);
@endphp
<style>
.public-shell{width:min(1180px,calc(100% - 32px));margin:auto}
.public-header{position:sticky;top:0;z-index:100;background:rgba(2,10,16,.9);-webkit-backdrop-filter:blur(18px);backdrop-filter:blur(18px);border-bottom:1px solid rgba(86,210,238,.14);box-shadow:0 10px 35px rgba(0,0,0,.12)}
.public-header-top{min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:24px;border-bottom:1px solid rgba(86,210,238,.10)}
.public-brand{display:flex;align-items:center;gap:11px;min-width:0;flex:0 1 auto;color:#effcff!important;text-decoration:none!important}.public-brand:visited,.public-brand:hover,.public-brand:active{color:#effcff!important;text-decoration:none!important}
.public-brand img,.public-brand-fallback{width:42px;height:42px;flex:0 0 42px}.public-brand img{object-fit:contain;border-radius:10px}.public-brand-fallback{display:grid;place-items:center;border-radius:10px;color:#43d1f0;border:1px solid rgba(86,210,238,.15)}
.public-brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;line-height:1.2;font-weight:800;letter-spacing:-.02em}
.public-header-tools{display:flex;align-items:center;justify-content:flex-end;gap:7px;min-width:0;flex:0 0 auto}.public-socials{display:flex;align-items:center;gap:4px}
.public-social{width:34px;height:34px;display:grid;place-items:center;border:1px solid transparent;border-radius:9px;color:#8eaab4!important;text-decoration:none!important;transition:color .2s,background .2s,border-color .2s,transform .2s}.public-social:hover{color:#8bf3ff!important;background:rgba(67,209,240,.08);border-color:rgba(86,210,238,.16);transform:translateY(-1px)}.public-social i{font-size:13px}
.public-header-divider{width:1px;height:26px;margin:0 7px;background:rgba(86,210,238,.20)}
.public-portal{display:inline-flex;align-items:center;gap:8px;min-height:36px;padding:0 13px;border:1px solid rgba(86,210,238,.18);border-radius:10px;background:rgba(67,209,240,.055);color:#c8e9ef!important;font-size:12px;font-weight:750;text-decoration:none!important;white-space:nowrap;transition:background .2s,border-color .2s,color .2s,transform .2s}.public-portal:hover{color:#fff!important;background:rgba(67,209,240,.11);border-color:rgba(86,210,238,.32);transform:translateY(-1px)}.public-portal i{color:#55d8f1;font-size:12px}
.public-header-nav{min-height:50px;display:flex;align-items:center}.public-menu{width:100%;display:flex;align-items:center;justify-content:center;gap:2px;min-width:0;overflow-x:auto;overflow-y:hidden;white-space:nowrap;scrollbar-width:none;-webkit-overflow-scrolling:touch}.public-menu::-webkit-scrollbar{display:none}
.public-menu a,.public-menu a:visited{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 11px;border-radius:8px;color:#9fb8c0!important;font-size:12px;line-height:1.2;font-weight:600;text-decoration:none!important;white-space:nowrap;flex:0 0 auto;transition:color .2s,background .2s}.public-menu a:hover,.public-menu a:active{color:#effcff!important;background:rgba(67,209,240,.08);text-decoration:none!important}.public-menu a[aria-current=page]{color:#8bf3ff!important;background:rgba(67,209,240,.07)}
.public-menu-toggle{display:none;width:44px;height:44px;border:1px solid rgba(86,210,238,.15);border-radius:11px;background:#06141d;color:#fff;align-items:center;justify-content:center;cursor:pointer;flex:0 0 auto}.public-menu-toggle svg{width:20px;height:20px;display:block}
body{font-size:16px!important}main p,main li,main td,main th,main label,main .body,main .rich,main .bio,main .bio-full,main .contact,main .action,main .empty,main .date,main .more,main .head p{font-size:16px!important;line-height:1.75!important}main button,main input,main select,main textarea{font-size:16px!important;line-height:1.4}main small{font-size:14px!important;line-height:1.6}.eyebrow{font-size:12px!important}
@media(max-width:1000px) and (min-width:721px){.public-shell{width:min(1180px,calc(100% - 24px))}.public-header-top{gap:12px}.public-brand-name{font-size:14px}.public-menu{justify-content:flex-start}.public-menu a,.public-menu a:visited{font-size:11px;padding-left:8px;padding-right:8px}.public-portal{font-size:11px;padding:0 10px}.public-social{width:30px;height:30px}.public-header-divider{margin:0 4px}}
@media(max-width:720px){.public-shell{width:calc(100% - 24px)}.public-header-top{min-height:70px;border-bottom:0}.public-header-tools{display:none}.public-brand-name{font-size:15px}.public-menu-toggle{display:flex}.public-header-nav{display:none;position:absolute;top:70px;left:12px;right:12px;min-height:auto;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:14px;box-shadow:0 20px 55px rgba(0,0,0,.3)}.public-header-nav.is-open{display:flex}.public-menu{flex-direction:column;align-items:stretch;justify-content:flex-start;overflow:visible;white-space:normal}.public-menu a,.public-menu a:visited{justify-content:flex-start;min-height:44px;padding:10px 13px;font-size:15px}}
@media(max-width:400px){.public-brand-name{font-size:14px}.public-brand img,.public-brand-fallback{width:40px;height:40px;flex-basis:40px}}
</style>
<header class="public-header">
    <div class="public-shell">
        <div class="public-header-top">
            <a class="public-brand" href="{{ route('home') }}" aria-label="{{ $publicName }}">
                @if($publicLogo)<img src="{{ asset('storage/'.ltrim($publicLogo,'/')) }}" alt="{{ $publicName }}">@else<span class="public-brand-fallback" aria-hidden="true">⚡</span>@endif
                <span class="public-brand-name">{{ $publicName }}</span>
            </a>
            <div class="public-header-tools">
                @if($publicSocials->isNotEmpty())
                    <div class="public-socials" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a class="public-social" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"><i class="{{ $social['icon'] }}" aria-hidden="true"></i></a>
                        @endforeach
                    </div>
                    <span class="public-header-divider" aria-hidden="true"></span>
                @endif
                <a class="public-portal" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i><span>Portal Login</span></a>
            </div>
            <button class="public-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false">
                <svg class="public-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div class="public-header-nav">
            <nav class="public-menu" aria-label="Primary navigation">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                @foreach($publicNavPages as $navPage)<a href="{{ route('company.page',$navPage->slug) }}" @if(request()->is('company/'.$navPage->slug)) aria-current="page" @endif>{{ $navPage->title }}</a>@endforeach
                <a href="{{ route('management') }}" @if(request()->routeIs('management')) aria-current="page" @endif>Management Team</a>
                <a href="{{ route('site.gallery') }}" @if(request()->routeIs('site.gallery') || request()->routeIs('gallery.show')) aria-current="page" @endif>Gallery</a>
                <a href="{{ route('news.index') }}" @if(request()->routeIs('news.*')) aria-current="page" @endif>News &amp; Notices</a>
                <a href="{{ route('site.career') }}" @if(request()->routeIs('site.career')) aria-current="page" @endif>Career</a>
                <a href="{{ route('contact') }}" @if(request()->routeIs('contact*')) aria-current="page" @endif>Contact</a>
                <a href="{{ route('webmail.redirect') }}">Webmail</a>
            </nav>
        </div>
    </div>
</header>
<script>
(function(){const buttons=document.querySelectorAll('.public-menu-toggle');buttons.forEach(button=>{if(button.dataset.bound==='1')return;button.dataset.bound='1';const wrapper=button.closest('.public-shell');const menu=wrapper?.querySelector('.public-header-nav');if(!menu)return;const icon=button.querySelector('.public-menu-icon');const bars='<path d="M4 6h16M4 12h16M4 18h16"/>';const close='<path d="M6 6l12 12M18 6L6 18"/>';button.addEventListener('click',()=>{const open=menu.classList.toggle('is-open');button.setAttribute('aria-expanded',open?'true':'false');button.setAttribute('aria-label',open?'Close navigation':'Open navigation');if(icon)icon.innerHTML=open?close:bars});menu.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>{menu.classList.remove('is-open');button.setAttribute('aria-expanded','false');button.setAttribute('aria-label','Open navigation');if(icon)icon.innerHTML=bars}));window.addEventListener('resize',()=>{if(window.innerWidth>720){menu.classList.remove('is-open');button.setAttribute('aria-expanded','false');button.setAttribute('aria-label','Open navigation');if(icon)icon.innerHTML=bars}})})})();
</script>
