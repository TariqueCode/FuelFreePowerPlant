@php
    $publicBrand = $brand ?? [];
    $headerLabels = config('fuelfree.header', []);

    $headerSettings = \App\Models\SystemSetting::query()
        ->whereIn('key', [
            'header.company_name',
            'header.portal_label',
            'header.login_label',
        ])
        ->pluck('value', 'key');

    $publicName = $headerSettings->get('header.company_name');

    if (!$publicName) {
        $publicName = is_object($publicBrand)
            ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name'))
            : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    }

    $headerLabels['portal_label'] = $headerSettings->get(
        'header.portal_label',
        $headerLabels['portal_label'] ?? 'Portal'
    );

    $headerLabels['login_label'] = $headerSettings->get(
        'header.login_label',
        $headerLabels['login_label'] ?? 'Login'
    );
    $publicLogo = is_object($publicBrand)
        ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path'))
        : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);

    if (!$publicLogo) {
        $publicLogo = \App\Models\SystemSetting::query()->where('key', 'company.logo_path')->value('value');
    }

    $publicDisplayName = trim((string) $publicName);
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
                'color' => data_get(config('fuelfree.social.platforms'), $social->platform . '.color', '#39E6A6'),
            ])
            ->values()
            ->all()
    );

    $isPortalUser = auth()->check();
    $publicPortalUrl = $isPortalUser ? route('dashboard') : route('login');
@endphp

<style>
:root{--ff-cyan:#57e6ff;--ff-emerald:#39e6a6;--ff-bg:#020b12;--ff-surface:#061923;--ff-text:#effcff;--ff-muted:#8caab5;--ff-line:rgba(87,230,255,.18)}
.public-shell{width:min(1280px,calc(100% - 40px));margin:auto}
.public-header{position:sticky;top:0;z-index:100;background:linear-gradient(180deg,rgba(2,11,18,.97),rgba(4,18,27,.94));backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border-bottom:1px solid rgba(87,230,255,.16);box-shadow:0 10px 34px rgba(0,0,0,.22),0 1px 0 rgba(57,230,166,.06) inset}
.public-header-top{min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid rgba(87,230,255,.08)}
.public-brand{display:flex;align-items:center;gap:10px;min-width:0;flex:0 0 auto;color:var(--ff-text)!important;text-decoration:none!important}
.public-brand img,.public-brand-fallback{width:44px;height:44px;flex:0 0 44px}.public-brand img{object-fit:contain;border-radius:10px;filter:drop-shadow(0 0 9px rgba(87,230,255,.18))}
.public-brand-fallback{display:grid;place-items:center;border-radius:10px;color:var(--ff-cyan);border:1px solid rgba(87,230,255,.22);background:rgba(87,230,255,.035);box-shadow:0 0 20px rgba(87,230,255,.08)}
.public-brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;line-height:1.2;font-weight:800;letter-spacing:.01em}.public-brand-name-first{color:#39E6A6}.public-brand-name-rest{color:#57E6FF;margin-left:.3em}
.public-header-tools{display:flex;align-items:center;justify-content:flex-end;gap:7px;min-width:0;flex:0 0 auto}.public-header-socials{display:flex;align-items:center;gap:5px}.public-header-social{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;color:var(--ff-muted)!important;background:rgba(87,230,255,.025);border:1px solid rgba(87,230,255,.12);text-decoration:none!important;transition:color .18s ease,background .18s ease,border-color .18s ease,transform .18s ease}.public-header-social:hover{color:var(--social-color)!important;background:color-mix(in srgb,var(--social-color) 9%,transparent);border-color:color-mix(in srgb,var(--social-color) 30%,transparent);transform:translateY(-1px)}.public-header-social i{font-size:13px}.public-header-divider{width:1px;height:34px;background:linear-gradient(to bottom,transparent,rgba(87,230,255,.4),transparent);margin:0 4px}
.public-portal{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:0 13px;border:1px solid rgba(87,230,255,.28);border-radius:10px;background:linear-gradient(135deg,rgba(87,230,255,.09),rgba(57,230,166,.055));color:var(--ff-text)!important;font-size:11px;font-weight:800;text-decoration:none!important;white-space:nowrap;box-shadow:inset 0 1px 0 rgba(255,255,255,.06),0 0 18px rgba(87,230,255,.05);transition:.18s ease}.public-portal:hover,.public-portal:focus-visible{border-color:rgba(87,230,255,.52);box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 0 22px rgba(87,230,255,.13);outline:none}.public-portal i{color:var(--ff-cyan);font-size:10px}
.public-header-nav{display:flex;align-items:center;justify-content:center;min-width:0;height:46px}.public-menu{width:100%;display:flex;align-items:center;justify-content:center;gap:4px;min-width:0;white-space:nowrap}.public-menu a,.public-menu a:visited,.public-menu-dropdown-toggle{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 11px;border-radius:8px;color:var(--ff-muted)!important;font-size:12px;line-height:1.15;font-weight:650;text-decoration:none!important;white-space:nowrap;background:transparent;border:0}.public-menu a:hover,.public-menu a:active,.public-menu a:focus,.public-menu-dropdown-toggle:hover,.public-menu-dropdown-toggle:focus,.public-menu-dropdown-toggle[aria-expanded=true]{color:var(--ff-text)!important;background:linear-gradient(135deg,rgba(87,230,255,.09),rgba(57,230,166,.045));outline:none}.public-menu a[aria-current=page],.public-menu-dropdown-toggle[aria-current=page]{color:var(--ff-cyan)!important;background:rgba(87,230,255,.065);box-shadow:inset 0 -1px 0 rgba(87,230,255,.7)}
.public-menu-dropdown{position:relative;display:flex;align-items:center;flex:0 0 auto}.public-menu-dropdown-toggle{gap:6px;cursor:pointer;font:inherit}.public-menu-dropdown-chevron{width:8px;height:8px;border-right:1.5px solid currentColor;border-bottom:1.5px solid currentColor;transform:rotate(45deg) translateY(-2px)}.public-menu-dropdown-toggle[aria-expanded=true] .public-menu-dropdown-chevron{transform:rotate(225deg) translate(-1px,-1px)}.public-menu-dropdown-panel{position:absolute;top:100%;left:50%;min-width:220px;padding:8px;background:rgba(3,15,23,.985);border:1px solid rgba(87,230,255,.18);border-radius:12px;box-shadow:0 20px 52px rgba(0,0,0,.45),0 0 28px rgba(87,230,255,.045);opacity:0;visibility:hidden;pointer-events:none;transform:translate(-50%,-6px);transition:opacity .16s ease,transform .16s ease,visibility .16s ease;z-index:120}.public-menu-dropdown:hover .public-menu-dropdown-panel,.public-menu-dropdown.is-open .public-menu-dropdown-panel{opacity:1;visibility:visible;pointer-events:auto;transform:translate(-50%,0)}.public-menu-dropdown-panel a,.public-menu-dropdown-panel a:visited{display:flex;width:100%;min-height:40px;justify-content:flex-start;padding:9px 12px;font-size:13px;box-sizing:border-box}.public-menu-dropdown-panel .public-menu-dropdown{width:100%}.public-menu-dropdown-panel .public-menu-dropdown-toggle{width:100%;justify-content:space-between}.public-menu-dropdown-panel .public-menu-dropdown-panel{top:-8px;left:100%;transform:translate(0,0);margin-left:6px}.public-menu-dropdown-panel .public-menu-dropdown:hover>.public-menu-dropdown-panel,.public-menu-dropdown-panel .public-menu-dropdown.is-open>.public-menu-dropdown-panel{transform:translate(0,0)}
.public-menu-toggle{display:none;width:42px;height:42px;border:1px solid rgba(87,230,255,.22);border-radius:11px;background:linear-gradient(135deg,rgba(87,230,255,.07),rgba(57,230,166,.035));color:var(--ff-text);align-items:center;justify-content:center;cursor:pointer;box-shadow:0 0 18px rgba(87,230,255,.05);transition:.18s ease}.public-menu-toggle:hover,.public-menu-toggle:focus-visible{border-color:rgba(87,230,255,.45);box-shadow:0 0 22px rgba(87,230,255,.12);outline:none}.mobile-portal-separator,.mobile-menu-portal{display:none!important}
@media(max-width:1050px){.public-shell{width:min(100% - 28px,1280px)}.public-header-top{gap:12px}.public-menu{gap:2px}.public-menu a,.public-menu-dropdown-toggle{padding-inline:8px;font-size:11px}.public-header-socials{gap:3px}.public-header-social{width:32px;height:32px}}
@media(max-width:820px){.public-shell{width:calc(100% - 20px)}.public-header-top{min-height:60px;border-bottom:0}.public-header-socials,.public-header-divider,.public-header-tools .public-portal{display:none!important}.public-header-nav{display:none;position:absolute;top:calc(100% + 8px);left:10px;right:10px;height:auto;max-height:calc(100vh - 76px);overflow-y:auto;padding:10px;background:rgba(3,15,23,.985);border:1px solid rgba(87,230,255,.2);border-radius:16px;box-shadow:0 26px 76px rgba(0,0,0,.48),0 0 34px rgba(87,230,255,.06);overscroll-behavior:contain}.public-header-nav.is-open{display:flex}.public-menu-toggle{display:flex;width:40px;height:40px}.public-menu{flex-direction:column;align-items:stretch;gap:4px;white-space:normal}.public-menu>a,.public-menu-dropdown{width:100%}.public-menu>a,.public-menu-dropdown-toggle{width:100%;height:48px;min-height:48px;justify-content:flex-start;padding:0 15px;font-size:15px;box-sizing:border-box;border:1px solid rgba(87,230,255,.08);border-radius:12px;background:rgba(255,255,255,.015)}.public-menu-dropdown-toggle{justify-content:space-between}.public-menu>a:hover,.public-menu>a:focus,.public-menu-dropdown-toggle:hover,.public-menu-dropdown-toggle:focus,.public-menu-dropdown-toggle[aria-expanded=true]{background:linear-gradient(135deg,rgba(87,230,255,.09),rgba(57,230,166,.04));border-color:rgba(87,230,255,.2)}.public-menu-dropdown-panel{position:static;display:none;width:100%;min-width:0;padding:0 0 0 10px;background:transparent;border:0;box-shadow:none;transform:none;opacity:1;visibility:visible}.public-menu-dropdown.is-open .public-menu-dropdown-panel{display:block;pointer-events:auto}.public-menu-dropdown-panel a{height:42px;min-height:42px;padding:0 13px;border-left:1px solid rgba(87,230,255,.2);border-radius:0 9px 9px 0}.mobile-portal-separator{display:block;height:1px;margin:12px 12px;background:linear-gradient(90deg,transparent,rgba(87,230,255,.38),rgba(57,230,166,.3),transparent)}.mobile-menu-portal{display:flex!important;align-items:center;justify-content:center;gap:10px;min-height:50px!important;margin:2px 4px 4px;padding:10px 16px!important;border:1px solid rgba(87,230,255,.3)!important;border-radius:12px;background:linear-gradient(135deg,rgba(87,230,255,.12),rgba(57,230,166,.06))!important;color:var(--ff-text)!important;font-size:15px!important;font-weight:800;box-shadow:inset 0 1px 0 rgba(255,255,255,.07),0 0 22px rgba(87,230,255,.07)}.public-menu-toggle[aria-expanded=true]{border-color:rgba(87,230,255,.5);background:linear-gradient(135deg,rgba(87,230,255,.12),rgba(57,230,166,.07))}}
@media (prefers-reduced-motion:reduce){.public-header-social,.public-portal,.public-menu-toggle{transition:none!important}}
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
                @foreach($publicMenuItems as $menuItem)
                    @include('partials.public-menu-item', ['menuItem' => $menuItem])
                @endforeach
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
        window.addEventListener('resize', function(){ if(window.innerWidth > 820) setOpen(false); });
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
            if(window.innerWidth <= 820) setOpen(!dropdown.classList.contains('is-open'));
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
        window.addEventListener('resize', function(){ if(window.innerWidth > 820) setOpen(false); });
    });
})();
</script>

<style>
/* =========================================================
   FUELFREE GLOBAL HEADER — ALL DEVICE RESPONSIVE POLISH
   Scoped only to public global header.
   ========================================================= */

.public-header{
    width:100%;
    overflow:visible;
}

.public-header .public-shell{
    width:min(1280px,calc(100% - 40px));
    margin-inline:auto;
}

.public-header-top{
    width:100%;
    min-width:0;
}

.public-brand{
    min-width:0;
    max-width:min(48%,520px);
}

.public-brand-name{
    min-width:0;
    max-width:100%;
}

.public-header-tools{
    min-width:0;
    flex-shrink:1;
}

.public-header-socials{
    min-width:0;
    flex-wrap:nowrap;
}

.public-header-nav{
    min-width:0;
    overflow:visible;
}

.public-menu{
    min-width:0;
    overflow:visible;
}

.public-menu>a,
.public-menu-dropdown,
.public-menu-dropdown-toggle{
    flex:0 0 auto;
}

/* Large desktop */
@media (min-width:1201px){
    .public-header-top{
        min-height:68px;
    }

    .public-brand img,
    .public-brand-fallback{
        width:46px;
        height:46px;
        flex-basis:46px;
    }

    .public-brand-name{
        font-size:16px;
    }

    .public-header-nav{
        height:48px;
    }
}

/* Medium desktop / tablet landscape */
@media (max-width:1200px) and (min-width:901px){
    .public-header .public-shell{
        width:calc(100% - 28px);
    }

    .public-brand{
        max-width:40%;
    }

    .public-brand-name{
        font-size:14px;
    }

    .public-header-social{
        width:31px;
        height:31px;
    }

    .public-portal{
        min-height:34px;
        padding-inline:11px;
        font-size:10px;
    }

    .public-menu{
        gap:1px;
    }

    .public-menu>a,
    .public-menu-dropdown-toggle{
        padding-inline:7px;
        font-size:10.5px;
    }
}

/* Tablet / mobile */
@media (max-width:900px){
    .public-header{
        position:sticky;
        top:0;
        z-index:1000;
    }

    .public-header .public-shell{
        width:calc(100% - 24px);
    }

    .public-header-top{
        min-height:60px;
        gap:10px;
    }

    .public-brand{
        max-width:calc(100% - 52px);
        flex:1 1 auto;
    }

    .public-brand img,
    .public-brand-fallback{
        width:42px;
        height:42px;
        flex-basis:42px;
        border-radius:9px;
    }

    .public-brand-name{
        font-size:14px;
        line-height:1.15;
    }

    .public-menu-toggle{
        flex:0 0 40px;
    }

    .public-header-nav{
        left:8px;
        right:8px;
        width:auto;
    }
}

/* Small phones */
@media (max-width:520px){
    .public-header .public-shell{
        width:calc(100% - 16px);
    }

    .public-header-top{
        min-height:56px;
        gap:8px;
    }

    .public-brand{
        max-width:calc(100% - 48px);
    }

    .public-brand img,
    .public-brand-fallback{
        width:38px;
        height:38px;
        flex-basis:38px;
        border-radius:8px;
    }

    .public-brand-name{
        font-size:12.5px;
        letter-spacing:0;
    }

    .public-menu-toggle{
        width:38px;
        height:38px;
        flex-basis:38px;
        border-radius:10px;
    }

    .public-header-nav{
        top:calc(100% + 6px);
        left:4px;
        right:4px;
        max-height:calc(100dvh - 68px);
        padding:8px;
        border-radius:14px;
    }

    .public-menu>a,
    .public-menu-dropdown-toggle{
        min-height:46px;
        height:46px;
        padding-inline:13px;
        font-size:14px;
    }

    .public-menu-dropdown-panel{
        padding-left:8px;
    }

    .public-menu-dropdown-panel a{
        min-height:40px;
        height:40px;
        font-size:13px;
    }

    .mobile-menu-portal{
        min-height:47px!important;
        font-size:14px!important;
    }
}

/* Very narrow phones */
@media (max-width:360px){
    .public-header .public-shell{
        width:calc(100% - 12px);
    }

    .public-brand-name{
        font-size:11.5px;
    }

    .public-brand img,
    .public-brand-fallback{
        width:36px;
        height:36px;
        flex-basis:36px;
    }

    .public-menu-toggle{
        width:36px;
        height:36px;
        flex-basis:36px;
    }

    .public-header-nav{
        left:2px;
        right:2px;
    }
}

/* Accessibility + safe viewport behavior */
@media (prefers-reduced-motion:reduce){
    .public-header *,
    .public-header *::before,
    .public-header *::after{
        scroll-behavior:auto!important;
        transition:none!important;
        animation:none!important;
    }
}

/* Prevent horizontal overflow caused by nested navigation */
.public-header,
.public-header .public-shell,
.public-header-top,
.public-header-nav,
.public-menu,
.public-menu-dropdown{
    box-sizing:border-box;
}

.public-header img{
    max-width:100%;
}
</style>
