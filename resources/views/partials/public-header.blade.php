@php
    $publicBrand = $brand ?? [];
    $publicSettings = $settings ?? \App\Models\SystemSetting::query()->pluck('value','key');

    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    $publicNavPages = \App\Models\SiteContentItem::query()->where('type','company')->where('status','published')->where('show_in_navigation',true)->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')->orderBy('navigation_order')->orderByDesc('created_at')->get(['title','slug']);
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
    $showHeaderSocial = filter_var($publicSettings->get('header.show_social','1'), FILTER_VALIDATE_BOOLEAN);
    $showHeaderWebmail = filter_var($publicSettings->get('header.show_webmail','1'), FILTER_VALIDATE_BOOLEAN);
    $showHeaderPortal = filter_var($publicSettings->get('header.show_portal','1'), FILTER_VALIDATE_BOOLEAN);
    $isPortalUser = auth()->check();
    $publicPortalUrl = $isPortalUser ? route('dashboard') : route('login');
@endphp
<style>
.public-shell{width:min(1280px,calc(100% - 40px));margin:auto}
.public-header{position:sticky;top:0;z-index:100;background:rgba(2,10,16,.94);-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px);border-bottom:1px solid rgba(86,210,238,.14);box-shadow:0 8px 28px rgba(0,0,0,.10)}
.public-header-top{min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid rgba(86,210,238,.09)}
.public-brand{display:flex;align-items:center;gap:9px;min-width:0;flex:0 0 auto;color:#effcff!important;text-decoration:none!important}.public-brand:visited,.public-brand:hover,.public-brand:active{color:#effcff!important;text-decoration:none!important}
.public-brand img,.public-brand-fallback{width:42px;height:42px;flex:0 0 42px}.public-brand img{object-fit:contain;border-radius:8px}.public-brand-fallback{display:grid;place-items:center;border-radius:8px;color:#43d1f0;border:1px solid rgba(86,210,238,.15)}
.public-brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;line-height:1.2;font-weight:800;letter-spacing:-.02em}
.public-header-nav{display:flex;align-items:center;justify-content:center;min-width:0;height:46px}.public-menu{width:100%;display:flex;align-items:center;justify-content:center;gap:4px;min-width:0;overflow-x:auto;overflow-y:hidden;white-space:nowrap;scrollbar-width:none;-webkit-overflow-scrolling:touch}.public-menu::-webkit-scrollbar{display:none}
.public-menu a,.public-menu a:visited{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 10px;border-radius:8px;color:#92adb6!important;font-size:12px;line-height:1.15;font-weight:600;text-decoration:none!important;white-space:nowrap;flex:0 0 auto;transition:color .18s,background .18s}.public-menu a:hover,.public-menu a:active{color:#effcff!important;background:rgba(67,209,240,.08);text-decoration:none!important}.public-menu a[aria-current=page]{color:#8bf3ff!important;background:rgba(67,209,240,.07)}
.public-header-tools{display:flex;align-items:center;justify-content:flex-end;gap:7px;min-width:0;flex:0 0 auto}.public-header-socials{display:flex;align-items:center;gap:5px}.public-header-social{--social-color:#51d8f0;display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;color:#8faeb8!important;background:rgba(67,209,240,.035);border:1px solid rgba(86,210,238,.11);text-decoration:none!important;transition:color .18s,background .18s,border-color .18s,transform .18s,box-shadow .18s}.public-header-social:hover,.public-header-social:focus-visible,.public-header-social.is-touched{color:var(--social-color)!important;background:color-mix(in srgb,var(--social-color) 10%,transparent);border-color:color-mix(in srgb,var(--social-color) 40%,transparent);box-shadow:0 0 18px color-mix(in srgb,var(--social-color) 18%,transparent);transform:translateY(-1px)}.public-header-social i{font-size:13px}.public-header-divider{width:1px;height:34px;background:linear-gradient(to bottom,transparent,rgba(86,210,238,.38),transparent);margin:0 4px}.public-portal{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:0 12px;border:1px solid rgba(86,210,238,.18);border-radius:9px;background:rgba(67,209,240,.055);color:#c8e9ef!important;font-size:11px;font-weight:750;text-decoration:none!important;white-space:nowrap;transition:background .18s,border-color .18s,color .18s,transform .18s}.public-portal:hover{color:#fff!important;background:rgba(67,209,240,.11);border-color:rgba(86,210,238,.32);transform:translateY(-1px)}.public-portal i{color:#55d8f1;font-size:10px}.mobile-portal-separator,.mobile-menu-portal{display:none}
@media(max-width:720px){.public-header-socials,.public-header-divider{display:none!important}.public-header-tools .public-portal{display:none!important}.public-header-tools{margin-left:auto}.public-header-nav{top:58px}.mobile-portal-separator{display:block;height:1px;margin:10px 12px;background:linear-gradient(90deg,transparent,rgba(86,210,238,.38),transparent)}.public-menu a.mobile-menu-portal{display:flex;align-items:center;justify-content:center;gap:10px;min-height:48px!important;margin:2px 8px 4px;padding:10px 16px!important;border:1px solid rgba(86,210,238,.24);border-radius:12px;background:rgba(67,209,240,.06);color:#8bf3ff!important;font-size:15px;font-weight:800}.public-menu a.mobile-menu-portal i{color:#55d8f1;flex:0 0 auto}.public-menu a.mobile-menu-portal span{display:inline-block;margin-left:1px}}
@media(min-width:721px){.mobile-menu-portal,.mobile-portal-separator{display:none!important}}
.public-menu-toggle{display:none;width:42px;height:42px;border:1px solid rgba(86,210,238,.15);border-radius:11px;background:#06141d;color:#fff;align-items:center;justify-content:center;cursor:pointer;flex:0 0 auto}.public-menu-toggle svg{width:19px;height:19px;display:block}
body{font-size:16px!important}main p,main li,main td,main th,main label,main .body,main .rich,main .bio,main .bio-full,main .contact,main .action,main .empty,main .date,main .more,main .head p{font-size:16px!important;line-height:1.75!important}main button,main input,main select,main textarea{font-size:16px!important;line-height:1.4}main small{font-size:14px!important;line-height:1.6}.eyebrow{font-size:12px!important}
@media(min-width:1001px){.public-brand img,.public-brand-fallback{width:42px;height:42px;flex-basis:42px}.public-brand-name{font-size:16px}.public-menu{gap:4px}.public-menu a,.public-menu a:visited{font-size:12px;padding-left:11px;padding-right:11px}}
@media(max-width:1000px) and (min-width:721px){.public-shell{width:min(1280px,calc(100% - 28px))}.public-header-top{gap:12px;min-height:58px}.public-brand img,.public-brand-fallback{width:38px;height:38px;flex-basis:38px}.public-brand-name{font-size:14px}.public-header-divider{height:36px;margin:0 5px}.public-portal{font-size:10px;padding:0 10px}.public-menu{justify-content:flex-start}.public-menu a,.public-menu a:visited{font-size:11px;padding-left:8px;padding-right:8px}}
@media(max-width:720px){.public-shell{width:calc(100% - 20px)}.public-header-top{min-height:58px;border-bottom:0}.public-header-nav{display:none;position:absolute;top:58px;left:12px;right:12px;height:auto;min-height:auto;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:14px;box-shadow:0 20px 55px rgba(0,0,0,.3)}.public-header-nav.is-open{display:flex}.public-header-tools{display:flex;gap:3px}.public-menu-toggle{display:flex;width:38px;height:38px;border-radius:10px}.public-menu{flex-direction:column;align-items:stretch;justify-content:flex-start;overflow:visible;white-space:normal}.public-menu a,.public-menu a:visited{justify-content:flex-start;min-height:44px;padding:10px 13px;font-size:15px}}
@media(max-width:400px){.public-brand-name{font-size:13px}.public-brand img,.public-brand-fallback{width:32px;height:32px;flex-basis:32px}.public-portal{width:auto;min-width:30px;padding:0 9px;justify-content:center;gap:6px}.public-portal span{display:inline-block}}
</style>
<header class="public-header">
    <div class="public-shell">
        <div class="public-header-top">
            <a class="public-brand" href="{{ route('home') }}" aria-label="{{ $publicName }}">
                @if($publicLogo)<img src="{{ asset('storage/'.ltrim($publicLogo,'/')) }}" alt="{{ $publicName }}">@else<span class="public-brand-fallback" aria-hidden="true">⚡</span>@endif
                <span class="public-brand-name">{{ $publicName }}</span>
            </a>
            <div class="public-header-tools">
                @if($showHeaderSocial && $publicSocials)
                    <div class="public-header-socials" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a class="public-header-social" style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"><i class="{{ $social['icon'] ?: 'fa-solid fa-link' }}" aria-hidden="true"></i></a>
                        @endforeach
                    </div>
                    <span class="public-header-divider" aria-hidden="true"></span>
                @endif
                @if($showHeaderPortal)
                    <a class="public-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid {{ $isPortalUser ? 'fa-circle-user' : 'fa-right-to-bracket' }}" aria-hidden="true"></i><span>{{ $isPortalUser ? 'Portal' : 'Login' }}</span></a>
                @endif
            </div>
            <button class="public-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="public-navigation">
                <svg class="public-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="public-navigation" class="public-header-nav">
            <nav class="public-menu" aria-label="Primary navigation">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                @foreach($publicNavPages as $navPage)<a href="{{ route('company.page',$navPage->slug) }}" @if(request()->is('company/'.$navPage->slug)) aria-current="page" @endif>{{ $navPage->title }}</a>@endforeach
                <a href="{{ route('management') }}" @if(request()->routeIs('management')) aria-current="page" @endif>Management Team</a>
                <a href="{{ route('site.gallery') }}" @if(request()->routeIs('site.gallery') || request()->routeIs('gallery.show')) aria-current="page" @endif>Gallery</a>
                <a href="{{ route('news.index') }}" @if(request()->routeIs('news.*')) aria-current="page" @endif>News &amp; Notices</a>
                <a href="{{ route('site.career') }}" @if(request()->routeIs('site.career')) aria-current="page" @endif>Career</a>
                <a href="{{ route('contact') }}" @if(request()->routeIs('contact*')) aria-current="page" @endif>Contact</a>
                @if($showHeaderWebmail)<a href="{{ route('webmail.redirect') }}">Webmail</a>@endif
                @if($showHeaderPortal)
                    <span class="mobile-portal-separator" aria-hidden="true"></span>
                    <a class="mobile-menu-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid {{ $isPortalUser ? 'fa-circle-user' : 'fa-right-to-bracket' }}" aria-hidden="true"></i><span>{{ $isPortalUser ? 'Portal' : 'Login' }}</span></a>
                @endif
            </nav>
        </div>
    </div>
</header>
<script>
(function(){
  document.querySelectorAll('.public-menu-toggle').forEach(function(button){
    if(button.dataset.bound === '1') return;
    button.dataset.bound = '1';
    var shell = button.closest('.public-shell'), menu = shell && shell.querySelector('.public-header-nav');
    if(!menu) return;
    var icon = button.querySelector('.public-menu-icon');
    var bars = '<path d="M4 6h16M4 12h16M4 18h16"/>', close = '<path d="M6 6l12 12M18 6L6 18"/>';
    function setOpen(open){menu.classList.toggle('is-open',open);button.setAttribute('aria-expanded',open?'true':'false');button.setAttribute('aria-label',open?'Close navigation':'Open navigation');if(icon)icon.innerHTML=open?close:bars;}
    button.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();setOpen(!menu.classList.contains('is-open'));});
    menu.querySelectorAll('a').forEach(function(link){link.addEventListener('click',function(){setOpen(false);});});
    document.addEventListener('click',function(e){if(menu.classList.contains('is-open')&&!shell.contains(e.target))setOpen(false);});
    window.addEventListener('resize',function(){if(window.innerWidth>720)setOpen(false);});
  });
})();
</script>
<script>(function(){document.querySelectorAll('.public-header-social').forEach(function(el){el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});el.addEventListener('blur',function(){el.classList.remove('is-touched')});});})();</script>
