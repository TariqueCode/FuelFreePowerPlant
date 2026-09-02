@php
    $publicBrand = $brand ?? [];
    $headerLabels = config('fuelfree.header', []);
    $publicName = is_object($publicBrand)
        ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name'))
        : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicLogo = is_object($publicBrand)
        ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path'))
        : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);

    if (!$publicLogo) {
        $publicLogo = \App\Models\SystemSetting::query()->where('key', 'company.logo_path')->value('value');
    }

    // Keep the registered brand spelling intact, but separate the two words
    // in the public header when the stored value is the concatenated form.
    $publicDisplayName = preg_replace('/^FUEL\\s*FREE\\s*POWER\\s*PLANT$/i', 'FUELFREE POWERPLANT', trim((string) $publicName));
    $publicNameParts = preg_split('/\\s+/', $publicDisplayName, 2);
    $publicNameFirst = $publicNameParts[0] ?? '';
    $publicNameRest = $publicNameParts[1] ?? '';

    $publicMenuItems = collect();
    if (\Illuminate\Support\Facades\Schema::hasTable('navigation_menu_items')) {
        $publicMenuItems = app(\App\Services\PublicNavigationService::class)->tree('main');
    }

    $publicSocials = \Illuminate\Support\Facades\Cache::remember(
        'public.social-links',
        600,
        fn () => \App\Models\SocialLink::active()
            ->get(['platform', 'label', 'url', 'icon'])
            ->map(fn ($social) => [
                'platform' => $social->platform,
                'label' => $social->label,
                'url' => $social->url,
                'icon' => $social->icon,
                'color' => data_get(config('fuelfree.social.platforms'), $social->platform . '.color', '#51D8F0'),
            ])
            ->values()
            ->all()
    );

    $isPortalUser = auth()->check();
    $publicPortalUrl = $isPortalUser ? route('dashboard') : route('login');
@endphp

<style>
.public-shell{width:min(1280px,calc(100% - 40px));margin:auto}
.public-header{position:sticky;top:0;z-index:100;background:rgba(2,10,16,.94);backdrop-filter:blur(16px);border-bottom:1px solid rgba(86,210,238,.14);box-shadow:0 8px 28px rgba(0,0,0,.10)}
.public-header-top{min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid rgba(86,210,238,.09)}
.public-brand{display:flex;align-items:center;gap:9px;min-width:0;flex:0 0 auto;color:#effcff!important;text-decoration:none!important}
.public-brand img,.public-brand-fallback{width:42px;height:42px;flex:0 0 42px}.public-brand img{object-fit:contain;border-radius:8px}
.public-brand-fallback{display:grid;place-items:center;border-radius:8px;color:#43d1f0;border:1px solid rgba(86,210,238,.15)}
.public-brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;line-height:1.2;font-weight:800}
.public-brand-name-first{color:#51d8f0}.public-brand-name-rest{color:#effcff;margin-left:.3em}
.public-header-tools{display:flex;align-items:center;justify-content:flex-end;gap:7px;min-width:0;flex:0 0 auto}
.public-header-socials{display:flex;align-items:center;gap:5px}.public-header-social{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;color:#8faeb8!important;background:rgba(67,209,240,.035);border:1px solid rgba(86,210,238,.11);text-decoration:none!important}
.public-header-social:hover{color:var(--social-color)!important;background:color-mix(in srgb,var(--social-color) 10%,transparent)}
.public-header-social i{font-size:13px}.public-header-divider{width:1px;height:34px;background:linear-gradient(to bottom,transparent,rgba(86,210,238,.38),transparent);margin:0 4px}
.public-portal{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:0 12px;border:1px solid rgba(86,210,238,.18);border-radius:9px;background:rgba(67,209,240,.055);color:#c8e9ef!important;font-size:11px;font-weight:750;text-decoration:none!important;white-space:nowrap}
.public-portal i{color:#55d8f1;font-size:10px}
.public-header-nav{display:flex;align-items:center;justify-content:center;min-width:0;height:46px}
.public-menu{width:100%;display:flex;align-items:center;justify-content:center;gap:4px;min-width:0;white-space:nowrap}
.public-menu a,.public-menu a:visited,.public-menu-dropdown-toggle{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 11px;border-radius:8px;color:#92adb6!important;font-size:12px;line-height:1.15;font-weight:600;text-decoration:none!important;white-space:nowrap;background:transparent;border:0}
.public-menu a:hover,.public-menu a:active,.public-menu a:focus,.public-menu-dropdown-toggle:hover,.public-menu-dropdown-toggle:focus,.public-menu-dropdown-toggle[aria-expanded=true]{color:#effcff!important;background:rgba(67,209,240,.08);outline:none}
.public-menu a[aria-current=page],.public-menu-dropdown-toggle[aria-current=page]{color:#8bf3ff!important;background:rgba(67,209,240,.07)}
.public-menu-dropdown{position:relative;display:flex;align-items:center;flex:0 0 auto}
.public-menu-dropdown-toggle{gap:6px;cursor:pointer;font:inherit}.public-menu-dropdown-chevron{width:8px;height:8px;border-right:1.5px solid currentColor;border-bottom:1.5px solid currentColor;transform:rotate(45deg) translateY(-2px)}
.public-menu-dropdown-toggle[aria-expanded=true] .public-menu-dropdown-chevron{transform:rotate(225deg) translate(-1px,-1px)}
.public-menu-dropdown-panel{position:absolute;top:100%;left:50%;min-width:220px;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:12px;box-shadow:0 18px 48px rgba(0,0,0,.34);opacity:0;visibility:hidden;pointer-events:none;transform:translate(-50%,-6px);transition:.16s;z-index:120}
.public-menu-dropdown:hover .public-menu-dropdown-panel,.public-menu-dropdown.is-open .public-menu-dropdown-panel{opacity:1;visibility:visible;pointer-events:auto;transform:translate(-50%,0)}
.public-menu-dropdown-panel a,.public-menu-dropdown-panel a:visited{display:flex;width:100%;min-height:40px;justify-content:flex-start;padding:9px 12px;font-size:13px;box-sizing:border-box}.public-menu-dropdown-panel .public-menu-dropdown{width:100%}.public-menu-dropdown-panel .public-menu-dropdown-toggle{width:100%;justify-content:space-between}.public-menu-dropdown-panel .public-menu-dropdown-panel{top:-8px;left:100%;transform:translate(0,0);margin-left:6px}.public-menu-dropdown-panel .public-menu-dropdown:hover>.public-menu-dropdown-panel,.public-menu-dropdown-panel .public-menu-dropdown.is-open>.public-menu-dropdown-panel{transform:translate(0,0)}
.public-menu-toggle{display:none;width:42px;height:42px;border:1px solid rgba(86,210,238,.15);border-radius:11px;background:#06141d;color:#fff;align-items:center;justify-content:center;cursor:pointer}
.mobile-portal-separator,.mobile-menu-portal{display:none!important}
@media(max-width:720px){
 .public-shell{width:calc(100% - 20px)}.public-header-top{min-height:58px;border-bottom:0}
 .public-header-socials,.public-header-divider,.public-header-tools .public-portal{display:none!important}
 .public-header-nav{display:none;position:absolute;top:58px;left:10px;right:10px;height:auto;padding:8px;background:rgba(4,18,26,.98);border:1px solid rgba(86,210,238,.15);border-radius:14px;box-shadow:0 20px 55px rgba(0,0,0,.3)}
 .public-header-nav.is-open{display:flex}.public-menu-toggle{display:flex;width:38px;height:38px}
 .public-menu{flex-direction:column;align-items:stretch;gap:0;white-space:normal}.public-menu>a,.public-menu-dropdown{width:100%}
 .public-menu>a,.public-menu-dropdown-toggle{width:100%;height:44px;min-height:44px;justify-content:flex-start;padding:0 13px;font-size:15px;box-sizing:border-box}
 .public-menu-dropdown-toggle{justify-content:space-between}.public-menu-dropdown-panel{position:static;display:none;width:100%;min-width:0;padding:0 0 0 10px;background:transparent;border:0;box-shadow:none;transform:none;opacity:1;visibility:visible}
 .public-menu-dropdown.is-open .public-menu-dropdown-panel{display:block;pointer-events:auto}.public-menu-dropdown-panel a{height:42px;min-height:42px;padding:0 13px;border-left:1px solid rgba(86,210,238,.18);border-radius:0 9px 9px 0}
 .mobile-portal-separator{display:block;height:1px;margin:10px 12px;background:linear-gradient(90deg,transparent,rgba(86,210,238,.38),transparent)}
 .mobile-menu-portal{display:flex!important;align-items:center;justify-content:center;gap:10px;min-height:48px!important;margin:2px 8px 4px;padding:10px 16px!important;border:1px solid rgba(86,210,238,.24)!important;border-radius:12px;background:rgba(67,209,240,.06)!important;color:#8bf3ff!important;font-size:15px!important;font-weight:800}
}
</style>

<header class="public-header">
    <div class="public-shell">
        <div class="public-header-top">
            <a class="public-brand" href="{{ route('home') }}" aria-label="{{ $publicDisplayName }}">
                @if($publicLogo)
                    <img src="{{ asset('storage/'.ltrim($publicLogo,'/')) }}" alt="{{ $publicDisplayName }}">
                @else
                    <span class="public-brand-fallback" aria-hidden="true">⚡</span>
                @endif
                <span class="public-brand-name">
                    <span class="public-brand-name-first">{{ $publicNameFirst }}</span>
                    @if($publicNameRest) <span class="public-brand-name-rest">{{ $publicNameRest }}</span>@endif
                </span>
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
                <a class="public-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid {{ $isPortalUser ? 'fa-circle-user' : 'fa-right-to-bracket' }}" aria-hidden="true"></i>
                    <span>{{ $isPortalUser ? ($headerLabels['portal_label'] ?? 'Portal') : ($headerLabels['login_label'] ?? 'Login') }}</span>
                </a>
            </div>

            <button class="public-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="public-navigation">
                <svg class="public-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="public-navigation" class="public-header-nav">
            <nav class="public-menu" aria-label="Primary navigation">
                @forelse($publicMenuItems as $menuItem)
                    @include('partials.public-menu-item', ['menuItem' => $menuItem])
                @empty
                    <a href="{{ route('home') }}">{{ $headerLabels['home_label'] ?? 'Home' }}</a>
                    <div class="public-menu-dropdown" data-fallback-company>
                        <button class="public-menu-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false">
                            <span>Company</span><span class="public-menu-dropdown-chevron" aria-hidden="true"></span>
                        </button>
                        <div class="public-menu-dropdown-panel">
                            <a href="{{ route('site.about') }}">{{ $headerLabels['about_label'] ?? 'About Us' }}</a>
                            <a href="{{ route('site.plants') }}">{{ $headerLabels['plants_label'] ?? 'Our Plants' }}</a>
                            <a href="{{ route('site.future-project') }}">{{ $headerLabels['future_project_label'] ?? 'Future Project' }}</a>
                            <a href="{{ route('site.solutions') }}">{{ $headerLabels['solutions_label'] ?? 'Solutions' }}</a>
                        </div>
                    </div>
                    <a href="{{ route('management') }}">{{ $headerLabels['management_label'] ?? 'Board of Directors' }}</a>
                    <a href="{{ route('site.gallery') }}">{{ $headerLabels['gallery_label'] ?? 'Gallery' }}</a>
                    <a href="{{ route('news.index') }}">{{ $headerLabels['news_label'] ?? 'News & Notices' }}</a>
                    <a href="{{ route('site.career') }}">{{ $headerLabels['career_label'] ?? 'Career' }}</a>
                    <a href="{{ route('contact') }}">{{ $headerLabels['contact_label'] ?? 'Contact' }}</a>
                    <a href="{{ route('webmail.redirect') }}" target="_blank" rel="noopener noreferrer">{{ $headerLabels['webmail_label'] ?? 'Webmail' }}</a>
                @endforelse
                <span class="mobile-portal-separator" aria-hidden="true"></span>
                <a class="mobile-menu-portal" href="{{ $publicPortalUrl }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid {{ $isPortalUser ? 'fa-circle-user' : 'fa-right-to-bracket' }}" aria-hidden="true"></i>
                    <span>{{ $isPortalUser ? ($headerLabels['portal_label'] ?? 'Portal') : ($headerLabels['login_label'] ?? 'Login') }}</span>
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
        var icon = button.querySelector('.public-menu-icon');
        if(!menu) return;
        function setOpen(open){
            menu.classList.toggle('is-open', open);
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
            if(icon) icon.innerHTML = open
                ? '<path d="M6 6l12 12M18 6L6 18"/>'
                : '<path d="M4 6h16M4 12h16M4 18h16"/>';
        }
        button.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); setOpen(!menu.classList.contains('is-open')); });
        menu.querySelectorAll('a').forEach(function(link){ link.addEventListener('click', function(){ setOpen(false); }); });
        document.addEventListener('click', function(e){ if(menu.classList.contains('is-open') && !shell.contains(e.target)) setOpen(false); });
        window.addEventListener('resize', function(){ if(window.innerWidth > 720) setOpen(false); });
    });

    document.querySelectorAll('.public-menu-dropdown').forEach(function(dropdown){
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
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape') {
                document.querySelectorAll('.public-menu-dropdown.is-open').forEach(function(openDropdown){
                    var openToggle = openDropdown.querySelector(':scope > .public-menu-dropdown-toggle');
                    openDropdown.classList.remove('is-open');
                    if(openToggle) openToggle.setAttribute('aria-expanded','false');
                });
            }
        });
        window.addEventListener('resize', function(){ if(window.innerWidth > 720) setOpen(false); });
    });
})();
</script>
