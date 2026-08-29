@php
    $publicBrand = $brand ?? [];
    $headerLabels = config('fuelfree.header', []);
    $publicName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    $publicNameParts = preg_split('/\s+/', trim((string) $publicName), 2);
    $publicNameFirst = $publicNameParts[0] ?? '';
    $publicNameRest = $publicNameParts[1] ?? '';
    $publicNavPages = \App\Models\SiteContentItem::query()->where('type','company')->where('status','published')->where('show_in_navigation',true)->orderByRaw("CASE WHEN slug = 'about-us' THEN 0 ELSE 1 END")->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')->orderBy('navigation_order')->orderByDesc('created_at')->get(['title','slug']);
    $publicCompanyActive = request()->routeIs('site.about') || request()->routeIs('company.page');
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
    $isPortalUser = auth()->check();
    $publicPortalUrl = $isPortalUser ? route('dashboard') : route('login');
@endphp
<style>
.public-shell{width:min(1280px,calc(100% - 40px));margin:auto}
.public-header{position:sticky;top:0;z-index:100;background:rgba(2,10,16,.94);-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px);border-bottom:1px solid rgba(86,210,238,.14);box-shadow:0 8px 28px rgba(0,0,0,.10)}
.public-header-top{min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid rgba(86,210,238,.09)}
.public-brand{display:flex;align-items:center;gap:9px;min-width:0;flex:0 0 auto;color:#effcff!important;text-decoration:none!important}.public-brand:visited,.public-brand:hover,.public-brand:active{color:#effcff!important;text-decoration:none!important}
.public-brand img,.public-brand-fallback{width:42px;height:42px;flex:0 0 42px}.public-brand img{object-fit:contain;border-radius:8px}.public-brand-fallback{display:grid;place-items:center;border-radius:8px;color:#43d1f0;border:1px solid rgba(86,210,238,.15)}
.public-brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;line-height:1.2;font-weight:800;letter-spacing:-.02em}.public-brand-name-first{color:#51d8f0}.public-brand-name-rest{color:#effcff}
.public-header-nav{display:flex;align-items:center;justify-content:center;min-width:0;height:46px}.public-menu{width:100%;display:flex;align-items:center;justify-content:center;gap:4px;min-width:0;overflow-x:auto;overflow-y:hidden;white-space:nowrap;scrollbar-width:none;-webkit-overflow-scrolling:touch}.public-menu::-webkit-scrollbar{display:none}
.public-menu a,.public-menu a:visited{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 10px;border-radius:8px;color:#92adb6!important;font-size:12px;line-height:1.15;font-weight:600;text-decoration:none!important;white-space:nowrap;flex:0 0 auto;transition:color .18s,background .18s}.public-menu a:hover,.public-menu a:active,.public-menu a:focus,.public-menu a:focus-visible{color:#effcff!important;background:rgba(67,209,240,.08);text-decoration:none!important;outline:none!important;box-shadow:none!important;border:0!important;-webkit-tap-highlight-color:transparent}.public-menu a[aria-current=page]{color:#8bf3ff!important;background:rgba(67,209,240,.07)}
.public-menu-dropdown{position:relative;display:flex;align-items:center;flex:0 0 auto}.public-menu-dropdown-toggle{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:34px;padding:6px 10px;border:0;border-radius:8px;background:transparent;color:#92adb6;font:inherit;font-size:12px;line-height:1.15;font-weight:600;cursor:pointer;white-space:nowrap;transition:color .18s,background .18s}.public-menu-dropdown-toggle:hover,.public-menu-dropdown-toggle:focus,.public-menu-dropdown-toggle:focus-visible,.public-menu-dropdown-toggle[aria-expanded=true]{color:#effcff;background:rgba(67,209,240,.08);outline:none!important;box-shadow:none!important;-webkit-tap-highlight-color:transparent}.public-menu-dropdown-toggle[aria-current=page]{color:#8bf3ff;background:rgba(67,209,240,.07)}.public-menu-dropdown-chevron{width:8px;height:8px;border-right:1.5px solid currentColor;border-bottom:1.5px solid currentColor;transform:rotate(45deg) translateY(-2px);transition:transform .18s}.public-menu-dropdown-toggle[aria-expanded=true] .public-menu-dropdown-chevron{transform:rotate(225deg) translate(-1px,-1px)}
.public-menu-dropdown-panel{position:absolute;top:100%;left:50%;min-width:220px;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:12px;box-shadow:0 18px 48px rgba(0,0,0,.34);opacity:0;visibility:hidden;pointer-events:none;transform:translate(-50%, -6px);transition:opacity .16s,visibility .16s,transform .16s;z-index:120}.public-menu-dropdown:hover .public-menu-dropdown-panel,.public-menu-dropdown.is-open .public-menu-dropdown-panel{opacity:1;visibility:visible;pointer-events:auto;transform:translate(-50%,0)}.public-menu-dropdown-panel a,.public-menu-dropdown-panel a:visited{display:flex;width:100%;min-height:40px;justify-content:flex-start;padding:9px 12px;font-size:13px;border-radius:9px}.public-menu-dropdown-panel a:hover,.public-menu-dropdown-panel a:active,.public-menu-dropdown-panel a:focus,.public-menu-dropdown-panel a:focus-visible{background:rgba(67,209,240,.08);outline:0!important;box-shadow:none!important;-webkit-tap-highlight-color:transparent}
.public-header-tools{display:flex;align-items:center;justify-content:flex-end;gap:7px;min-width:0;flex:0 0 auto}.public-header-socials{display:flex;align-items:center;gap:5px}.public-header-social{--social-color:#51d8f0;display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;color:#8faeb8!important;background:rgba(67,209,240,.035);border:1px solid rgba(86,210,238,.11);text-decoration:none!important;transition:color .18s,background .18s,border-color .18s,transform .18s,box-shadow .18s}.public-header-social:hover,.public-header-social:focus-visible,.public-header-social.is-touched{color:var(--social-color)!important;background:color-mix(in srgb,var(--social-color) 10%,transparent);border-color:color-mix(in srgb,var(--social-color) 40%,transparent);box-shadow:0 0 18px color-mix(in srgb,var(--social-color) 18%,transparent);transform:translateY(-1px)}.public-header-social i{font-size:13px}.public-header-divider{width:1px;height:34px;background:linear-gradient(to bottom,transparent,rgba(86,210,238,.38),transparent);margin:0 4px}.public-portal{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:0 12px;border:1px solid rgba(86,210,238,.18);border-radius:9px;background:rgba(67,209,240,.055);color:#c8e9ef!important;font-size:11px;font-weight:750;text-decoration:none!important;white-space:nowrap;transition:background .18s,border-color .18s,color .18s,transform .18s}.public-portal:hover{color:#fff!important;background:rgba(67,209,240,.11);border-color:rgba(86,210,238,.32);transform:translateY(-1px)}.public-portal i{color:#55d8f1;font-size:10px}.mobile-portal-separator,.mobile-menu-portal{display:none}
@media(max-width:720px){
  .public-header-socials,.public-header-divider{display:none!important}
  .public-header-tools .public-portal{display:none!important}
  .public-header-tools{margin-left:auto}
  .public-header-nav{top:58px}
  .mobile-portal-separator{display:block;height:1px;margin:10px 12px;background:linear-gradient(90deg,transparent,rgba(86,210,238,.38),transparent)}
  .public-menu a.mobile-menu-portal{display:flex;align-items:center;justify-content:center;gap:10px;min-height:48px!important;margin:2px 8px 4px;padding:10px 16px!important;border:1px solid rgba(86,210,238,.24);border-radius:12px;background:rgba(67,209,240,.06);color:#8bf3ff!important;font-size:15px;font-weight:800}
  .public-menu a.mobile-menu-portal i{color:#55d8f1;flex:0 0 auto}.public-menu a.mobile-menu-portal span{display:inline-block;margin-left:1px}
  .public-menu-dropdown{display:block;width:100%}
  .public-menu-dropdown-toggle{width:100%;min-height:48px;justify-content:space-between;padding:11px 13px;font-size:15px}
  .public-menu-dropdown-panel{position:static;min-width:0;width:100%;padding:4px 0 4px 10px;background:transparent;border:0;border-radius:0;box-shadow:none;opacity:1;visibility:visible;pointer-events:none;transform:none;display:none}
  .public-menu-dropdown.is-open .public-menu-dropdown-panel{display:block;pointer-events:auto}
  .public-menu-dropdown.is-open .public-menu-dropdown-panel{opacity:1;visibility:visible;transform:none}
  .public-menu-dropdown-panel a,.public-menu-dropdown-panel a:visited{min-height:42px;padding:9px 13px;font-size:14px;border-left:1px solid rgba(86,210,238,.18);border-radius:0 9px 9px 0}
}
@media(min-width:721px){.mobile-menu-portal,.mobile-portal-separator{display:none!important}}
.public-menu-toggle{display:none;width:42px;height:42px;border:1px solid rgba(86,210,238,.15);border-radius:11px;background:#06141d;color:#fff;align-items:center;justify-content:center;cursor:pointer;flex:0 0 auto}.public-menu-toggle svg{width:19px;height:19px;display:block}
@media(min-width:721px){.public-header-nav,.public-menu{overflow:visible!important;}}
@media(min-width:1001px){.public-social-label{display:inline-block}.public-brand img,.public-brand-fallback{width:42px;height:42px;flex-basis:42px}.public-brand-name{font-size:16px}.public-menu{gap:4px}.public-menu a,.public-menu a:visited,.public-menu-dropdown-toggle{font-size:12px;padding-left:11px;padding-right:11px}}
@media(max-width:1000px) and (min-width:721px){.public-shell{width:min(1280px,calc(100% - 28px))}.public-header-top{gap:12px;min-height:58px}.public-brand img,.public-brand-fallback{width:38px;height:38px;flex-basis:38px}.public-brand-name{font-size:14px}.public-header-divider{height:36px;margin:0 5px}.public-portal{font-size:10px;padding:0 10px}.public-menu{justify-content:flex-start}.public-menu a,.public-menu a:visited,.public-menu-dropdown-toggle{font-size:11px;padding-left:8px;padding-right:8px}}
@media(max-width:720px){.public-shell{width:calc(100% - 20px)}.public-header-top{min-height:58px;border-bottom:0}.public-header-nav{display:none;position:absolute;top:58px;left:12px;right:12px;height:auto;min-height:auto;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:14px;box-shadow:0 20px 55px rgba(0,0,0,.3)}.public-header-nav.is-open{display:flex}.public-header-tools{display:flex;gap:3px}.public-socials{max-width:30vw;gap:1px}.public-social{width:28px;height:28px;border-radius:7px}.public-social i{font-size:11px}.public-header-divider{height:34px;margin:0 5px}.public-portal{min-height:30px;padding:0 8px;border-radius:8px;font-size:10px;gap:5px}.public-brand-name{font-size:14px}.public-brand img,.public-brand-fallback{width:34px;height:34px;flex-basis:34px}.public-menu-toggle{display:flex;width:38px;height:38px;border-radius:10px}.public-menu{flex-direction:column;align-items:stretch;justify-content:flex-start;overflow:visible;white-space:normal}.public-menu a,.public-menu a:visited{justify-content:flex-start;min-height:44px;padding:10px 13px;font-size:15px}}
@media(max-width:400px){.public-brand-name{font-size:13px}.public-brand img,.public-brand-fallback{width:32px;height:32px;flex-basis:32px}.public-social{width:26px;height:26px}.public-portal span{display:none}.public-portal{width:auto;min-width:30px;padding:0 9px;justify-content:center;gap:6px}.public-portal span{display:inline-block}}
@media(max-width:720px){
  /* Compact mobile navigation: prevent page-specific/global styles from creating artificial gaps. */
  .public-menu{gap:0!important;row-gap:0!important}
  .public-menu > a,.public-menu > .public-menu-dropdown{margin:0!important}
  .public-menu > a,.public-menu-dropdown-toggle{line-height:1.2!important}
  .public-menu-dropdown-panel{margin:0!important;gap:0!important}
  .public-menu-dropdown-panel a,.public-menu-dropdown-panel a:visited{margin:0!important;line-height:1.2!important;min-height:42px!important;padding:9px 13px!important}
}
</style>
<header class="public-header">
    <div class="public-shell">
        <div class="public-header-top">
            <a class="public-brand" href="{{ route('home') }}" aria-label="{{ $publicName }}">
                @if($publicLogo)<img src="{{ asset('storage/'.ltrim($publicLogo,'/')) }}" alt="{{ $publicName }}">@else<span class="public-brand-fallback" aria-hidden="true">⚡</span>@endif
                <span class="public-brand-name"><span class="public-brand-name-first">{{ $publicNameFirst }}</span>@if($publicNameRest) <span class="public-brand-name-rest">{{ $publicNameRest }}</span>@endif</span>
            </a>
            <div class="public-header-tools">
                @if($publicSocials)
                    <div class="public-header-socials" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a class="public-header-social" style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] ?: 'fa-solid fa-link' }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                    <span class="public-header-divider" aria-hidden="true"></span>
                @endif
                @if($isPortalUser)
                    <a class="public-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-circle-user" aria-hidden="true"></i><span>{{ $headerLabels['portal_label'] ?? 'Portal' }}</span></a>
                @else
                    <a class="public-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i><span>{{ $headerLabels['login_label'] ?? 'Login' }}</span></a>
                @endif
            </div>
            <button class="public-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="public-navigation">
                <svg class="public-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="public-navigation" class="public-header-nav">
            <nav class="public-menu" aria-label="Primary navigation">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>{{ $headerLabels['home_label'] ?? 'Home' }}</a>
                <div class="public-menu-dropdown @if($publicCompanyActive) is-current @endif" data-company-menu>
                    <button class="public-menu-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="public-company-menu" @if($publicCompanyActive) aria-current="page" @endif>
                        <span>Company</span><span class="public-menu-dropdown-chevron" aria-hidden="true"></span>
                    </button>
                    <div id="public-company-menu" class="public-menu-dropdown-panel">
                        @foreach($publicNavPages as $navPage)
                            @php $navUrl = $navPage->slug === 'about-us' ? route('site.about') : route('company.page',$navPage->slug); @endphp
                            <a href="{{ $navUrl }}" @if(($navPage->slug === 'about-us' && request()->routeIs('site.about')) || ($navPage->slug !== 'about-us' && request()->routeIs('company.page') && request()->route('slug') === $navPage->slug)) aria-current="page" @endif>{{ $navPage->title }}</a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('management') }}" @if(request()->routeIs('management')) aria-current="page" @endif>{{ $headerLabels['management_label'] ?? 'Management Team' }}</a>
                <a href="{{ route('site.gallery') }}" @if(request()->routeIs('site.gallery') || request()->routeIs('gallery.show')) aria-current="page" @endif>{{ $headerLabels['gallery_label'] ?? 'Gallery' }}</a>
                <a href="{{ route('news.index') }}" @if(request()->routeIs('news.*')) aria-current="page" @endif>{{ $headerLabels['news_label'] ?? 'News &amp; Notices' }}</a>
                <a href="{{ route('site.career') }}" @if(request()->routeIs('site.career')) aria-current="page" @endif>{{ $headerLabels['career_label'] ?? 'Career' }}</a>
                <a href="{{ route('contact') }}" @if(request()->routeIs('contact*')) aria-current="page" @endif>{{ $headerLabels['contact_label'] ?? 'Contact' }}</a>
                <a href="{{ route('webmail.redirect') }}" target="_blank" rel="noopener noreferrer">{{ $headerLabels['webmail_label'] ?? 'Webmail' }}</a>
                <span class="mobile-portal-separator" aria-hidden="true"></span>
                <a class="mobile-menu-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid {{ $isPortalUser ? 'fa-circle-user' : 'fa-right-to-bracket' }}" aria-hidden="true"></i>
                    <span>{{ $isPortalUser ? 'Portal' : 'Login' }}</span>
                </a>
            </nav>
        </div>
    </div>
</header>
<script>
(function(){
  document.querySelectorAll('.public-menu-toggle').forEach(function(button){
    if(button.dataset.bound === '1') return;
    button.dataset.bound = '1';
    var shell = button.closest('.public-shell');
    var menu = shell && shell.querySelector('.public-header-nav');
    if(!menu) return;
    var icon = button.querySelector('.public-menu-icon');
    var bars = '<path d="M4 6h16M4 12h16M4 18h16"/>';
    var close = '<path d="M6 6l12 12M18 6L6 18"/>';
    function setOpen(open){
      menu.classList.toggle('is-open', open);
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      button.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
      if(icon) icon.innerHTML = open ? close : bars;
    }
    button.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); setOpen(!menu.classList.contains('is-open')); });
    menu.querySelectorAll('a').forEach(function(link){ link.addEventListener('click', function(){ setOpen(false); }); });
    document.addEventListener('click', function(e){ if(menu.classList.contains('is-open') && !shell.contains(e.target)) setOpen(false); });
    window.addEventListener('resize', function(){ if(window.innerWidth > 720) setOpen(false); });
  });
})();
</script>
<script>
(function(){
  document.querySelectorAll('[data-company-menu]').forEach(function(dropdown){
    var toggle = dropdown.querySelector('.public-menu-dropdown-toggle');
    if(!toggle || toggle.dataset.bound === '1') return;
    toggle.dataset.bound = '1';
    function setOpen(open){
      dropdown.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    toggle.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      if(window.innerWidth <= 720) setOpen(!dropdown.classList.contains('is-open'));
    });
    document.addEventListener('click', function(e){ if(!dropdown.contains(e.target)) setOpen(false); });
    window.addEventListener('resize', function(){ if(window.innerWidth > 720) setOpen(false); });
  });
})();
</script>
<script>
(function(){document.querySelectorAll('.public-header-social').forEach(function(el){el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});el.addEventListener('blur',function(){el.classList.remove('is-touched')});});})();
</script>
