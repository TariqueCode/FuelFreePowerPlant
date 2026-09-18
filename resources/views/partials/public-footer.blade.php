@php
    $publicBrand = $brand ?? [];
    $footerSettings = config('fuelfree.footer', []);

    $footerKeys = [
        'footer.company_name',
        'footer.tagline',
        'footer.technology',
        'footer.office_heading',
        'footer.address',
        'footer.contact_heading',
        'footer.email',
        'footer.phone',
        'footer.website',
        'footer.website_url',
        'footer.get_in_touch_label',
        'footer.get_in_touch_url',
        'footer.copyright_text',
        'design.footer.columns_enabled',
        'design.footer.links_enabled',
        'design.footer.social_enabled',
        'design.footer.contact_enabled',
        'design.footer.copyright_enabled',
    ];

    $footerSaved = \App\Models\SystemSetting::query()
        ->whereIn('key', $footerKeys)
        ->pluck('value', 'key');

    $footerVisible = fn ($key) => filter_var(
        $footerSaved->get(
            'design.footer.' . $key . '_enabled',
            '1'
        ),
        FILTER_VALIDATE_BOOLEAN
    );

    $publicFooterName = $footerSaved->get('footer.company_name');

    if (!$publicFooterName) {
        $publicFooterName = is_object($publicBrand)
            ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name'))
            : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    }

    $publicFooterTagline = $footerSaved->get(
        'footer.tagline',
        $footerSettings['tagline'] ?? (
            is_object($publicBrand)
                ? ($publicBrand->get('tagline') ?: $publicBrand->get('company.tagline') ?: config('fuelfree.company.tagline'))
                : ($publicBrand['tagline'] ?? $publicBrand['company.tagline'] ?? config('fuelfree.company.tagline'))
        )
    );

    $publicFooterTechnology = $footerSaved->get(
        'footer.technology',
        $footerSettings['technology'] ?? 'Fuel-Free Flywheel-Based Clean Energy Technology'
    );

    $publicFooterOfficeHeading = $footerSaved->get(
        'footer.office_heading',
        $footerSettings['office_heading'] ?? 'Office'
    );

    $publicFooterAddress = $footerSaved->get(
        'footer.address',
        $footerSettings['address'] ?? ''
    );

    $publicFooterContactHeading = $footerSaved->get(
        'footer.contact_heading',
        $footerSettings['contact_heading'] ?? 'Contact'
    );

    $publicFooterEmail = $footerSaved->get(
        'footer.email',
        $footerSettings['email'] ?? ''
    );

    $publicFooterPhone = $footerSaved->get(
        'footer.phone',
        $footerSettings['phone'] ?? ''
    );

    $publicFooterWebsite = $footerSaved->get(
        'footer.website',
        $footerSettings['website'] ?? ''
    );

    $publicFooterWebsiteUrl = $footerSaved->get(
        'footer.website_url',
        $footerSettings['website_url'] ?? ''
    );

    $publicFooterGetInTouchLabel = $footerSaved->get(
        'footer.get_in_touch_label',
        $footerSettings['get_in_touch_label'] ?? 'Get in touch'
    );

    $publicFooterGetInTouchUrl = $footerSaved->get(
        'footer.get_in_touch_url',
        $footerSettings['get_in_touch_url'] ?? route('contact')
    );

    $publicFooterCopyright = $footerSaved->get(
        'footer.copyright_text',
        $footerSettings['copyright_text'] ?? 'All rights reserved.'
    );

    $publicFooterLogo = is_object($publicBrand)
        ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path'))
        : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);

    if (!$publicFooterLogo) {
        $publicFooterLogo = \App\Models\SystemSetting::query()
            ->where('key', 'company.logo_path')
            ->value('value');
    }

    $publicFooterNameParts = preg_split(
        '/\s+/',
        trim((string) $publicFooterName),
        2
    );

    $publicFooterNameFirst = $publicFooterNameParts[0] ?? '';
    $publicFooterNameRest = $publicFooterNameParts[1] ?? '';
@endphp
@php
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#39E6A6')])->values()->all());
@endphp

<style>
/* =========================================================
   FUELFREE PREMIUM GLOBAL FOOTER
   Brand palette derived from the official logo:
   emerald power green + electric blue + silver/white.
   ========================================================= */
.public-footer{
    --footer-green:#35d86a;
    --footer-green-soft:rgba(53,216,106,.10);
    --footer-green-line:rgba(53,216,106,.22);
    --footer-blue:#168dff;
    --footer-blue-soft:rgba(22,141,255,.08);
    --footer-silver:#d7dde0;
    --footer-text:#f4fff8;
    --footer-muted:#91a99d;
    --footer-dim:#5f796d;
    position:relative;
    isolation:isolate;
    margin-top:72px;
    padding:64px 0 24px;
    border-top:1px solid rgba(53,216,106,.20);
    color:var(--footer-muted);
    font-size:14px;
    line-height:1.7;
    background:
        radial-gradient(circle at 10% 8%,rgba(53,216,106,.09),transparent 28%),
        radial-gradient(circle at 90% 14%,rgba(22,141,255,.065),transparent 25%),
        radial-gradient(circle at 50% 100%,rgba(53,216,106,.035),transparent 42%),
        linear-gradient(180deg,#06120d 0%,#03100b 46%,#020906 100%);
    overflow:hidden;
}
.public-footer::before{
    content:"";
    position:absolute;
    z-index:-1;
    top:0;
    left:7%;
    right:7%;
    height:1px;
    background:linear-gradient(90deg,transparent,rgba(53,216,106,.12),rgba(53,216,106,.78),rgba(22,141,255,.38),rgba(53,216,106,.12),transparent);
    box-shadow:0 0 18px rgba(53,216,106,.12);
}
.public-footer::after{
    content:"";
    position:absolute;
    z-index:-1;
    width:420px;
    height:420px;
    right:-220px;
    bottom:-260px;
    border:1px solid rgba(53,216,106,.055);
    border-radius:50%;
    box-shadow:0 0 0 36px rgba(22,141,255,.018),0 0 0 72px rgba(53,216,106,.014);
    pointer-events:none;
}
.public-footer-management{margin-top:32px}
.public-footer-shell{width:min(1180px,calc(100% - 40px));margin:0 auto}
.public-footer-grid{
    display:grid;
    grid-template-columns:minmax(0,1.22fr) minmax(0,.88fr) minmax(0,.88fr);
    gap:42px;
    padding-bottom:38px;
}
.public-footer-section{min-width:0}
.public-footer-brand-section{
    position:relative;
    padding:4px 42px 4px 0;
}
.public-footer-brand-section::after{
    content:"";
    position:absolute;
    top:0;
    right:0;
    width:1px;
    height:100%;
    background:linear-gradient(180deg,transparent,rgba(53,216,106,.34),rgba(215,221,224,.13),rgba(22,141,255,.16),transparent);
}
.public-footer-brand-row{
    display:flex;
    align-items:center;
    gap:18px;
    margin-bottom:15px;
}
.public-footer-logo{
    width:116px;
    height:116px;
    flex:0 0 116px;
    display:block;
    object-fit:contain;
    object-position:center;
    filter:drop-shadow(0 0 14px rgba(53,216,106,.24)) drop-shadow(0 0 24px rgba(22,141,255,.10));
}
.public-footer-brand{
    max-width:250px;
    color:var(--footer-text);
    font-size:25px;
    font-weight:850;
    line-height:1.08;
    letter-spacing:-.45px;
}
.public-footer-brand-first,
.public-footer-brand-rest{
    display:block;
    background:linear-gradient(110deg,#35d86a 0%,#a8f4bd 42%,#f3f7f8 82%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}
.public-footer-brand-rest{
    background:linear-gradient(110deg,#d7dde0 0%,#ffffff 52%,#8df0b0 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}
.public-footer-tagline{
    margin:0 0 7px;
    color:#72e99a;
    font-size:13px;
    font-weight:750;
    letter-spacing:.12px;
}
.public-footer-tech{
    max-width:410px;
    color:#789387;
    font-size:12px;
    line-height:1.75;
}
.public-footer-social-wrap{
    display:flex;
    align-items:center;
    gap:9px;
    flex-wrap:wrap;
    margin-top:22px;
}
.public-footer-social{
    --social-color:var(--footer-green);
    width:39px;
    height:39px;
    display:grid;
    place-items:center;
    border:1px solid rgba(53,216,106,.19);
    border-radius:12px;
    background:linear-gradient(145deg,rgba(53,216,106,.07),rgba(22,141,255,.025));
    color:#789387;
    text-decoration:none;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.035);
    transition:color .2s ease,background .2s ease,border-color .2s ease,transform .2s ease,box-shadow .2s ease;
}
.public-footer-social:hover,
.public-footer-social:focus-visible,
.public-footer-social.is-touched{
    color:var(--social-color);
    background:linear-gradient(145deg,rgba(53,216,106,.14),rgba(22,141,255,.055));
    border-color:color-mix(in srgb,var(--social-color) 52%,transparent);
    transform:translateY(-2px);
    box-shadow:0 10px 26px rgba(0,0,0,.22),0 0 20px rgba(53,216,106,.10);
    outline:none;
}
.public-footer-social i{font-size:14px}

.public-footer-accordion{margin:0}
.public-footer-summary{
    list-style:none;
    cursor:default;
    color:var(--footer-text);
}
.public-footer-summary::-webkit-details-marker{display:none}
.public-footer-summary .public-footer-summary-chevron{display:none}
.public-footer-heading{
    position:relative;
    margin:2px 0 18px;
    padding-left:14px;
    color:var(--footer-text);
    font-size:15px;
    font-weight:800;
    letter-spacing:.15px;
}
.public-footer-heading::before{
    content:"";
    position:absolute;
    left:0;
    top:3px;
    width:3px;
    height:17px;
    border-radius:4px;
    background:linear-gradient(180deg,var(--footer-green),var(--footer-blue));
    box-shadow:0 0 10px rgba(53,216,106,.20);
}
.public-footer-address{
    display:flex;
    gap:11px;
    align-items:flex-start;
    color:#8ba499;
    font-size:13px;
    line-height:1.8;
}
.public-footer-address span{max-width:270px}
.public-footer-address i{
    width:18px;
    flex:0 0 18px;
    margin-top:5px;
    color:var(--footer-green);
    text-align:center;
}
.public-footer-contact{display:grid;gap:13px}
.public-footer-contact a{
    display:flex;
    align-items:flex-start;
    gap:11px;
    color:#8ba499;
    font-size:13px;
    line-height:1.55;
    text-decoration:none;
    transition:color .2s ease,transform .2s ease;
}
.public-footer-contact a:hover,
.public-footer-contact a:focus-visible{
    color:#f4fff8;
    transform:translateX(3px);
    outline:none;
}
.public-footer-contact i{
    width:18px;
    height:18px;
    flex:0 0 18px;
    display:inline-grid;
    place-items:center;
    margin-top:2px;
    color:var(--footer-green);
    text-align:center;
    font-family:"Font Awesome 6 Free"!important;
    font-weight:900!important;
    font-style:normal;
}
.public-footer-bottom{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    padding-top:18px;
    border-top:1px solid rgba(53,216,106,.11);
    color:#61796e;
    font-size:12px;
}
.public-footer-developer{
    color:#4d675c;
    font-size:9px;
    line-height:1.4;
    white-space:nowrap;
}
.public-footer-developer a{
    color:#628174;
    text-decoration:none;
    transition:color .2s ease;
}
.public-footer-developer a:hover,
.public-footer-developer a:focus-visible{color:#9de8b4;outline:none}

@media(max-width:1000px){
    .public-footer-shell{width:min(100% - 32px,820px)}
    .public-footer-grid{
        grid-template-columns:minmax(0,1fr) minmax(0,1fr);
        gap:32px 28px;
    }
    .public-footer-brand-section{
        grid-column:1 / -1;
        padding:0 0 28px;
        border-bottom:1px solid rgba(53,216,106,.11);
    }
    .public-footer-brand-section::after{display:none}
    .public-footer-logo{width:108px;height:108px;flex-basis:108px}
}

@media(max-width:600px){
    .public-footer{
        margin-top:48px;
        padding:42px 0 20px;
    }
    .public-footer-management{margin-top:24px}
    .public-footer-shell{width:calc(100% - 24px)}
    .public-footer-grid{
        grid-template-columns:1fr;
        gap:12px;
        padding-bottom:28px;
    }
    .public-footer-brand-section{
        grid-column:auto;
        padding:0 0 28px;
        text-align:center;
        display:flex;
        flex-direction:column;
        align-items:center;
    }
    .public-footer-brand-row{
        flex-direction:column;
        justify-content:center;
        gap:11px;
        margin-bottom:11px;
    }
    .public-footer-logo{
        width:100px;
        height:100px;
        flex-basis:100px;
    }
    .public-footer-brand{
        width:100%;
        max-width:none;
        font-size:clamp(16px,4.7vw,21px);
        line-height:1.12;
        letter-spacing:-.25px;
        white-space:nowrap;
        text-align:center;
    }
    .public-footer-brand-first,
    .public-footer-brand-rest{
        display:inline;
        white-space:nowrap;
    }
    .public-footer-brand-first::after{content:" "}
    .public-footer-tagline{font-size:11px;margin-bottom:5px}
    .public-footer-tech{max-width:310px;font-size:11px}
    .public-footer-social-wrap{justify-content:center;margin-top:17px}
    .public-footer-section:not(.public-footer-brand-section){width:100%}
    .public-footer-accordion{
        border:1px solid rgba(53,216,106,.15);
        border-radius:14px;
        background:linear-gradient(145deg,rgba(53,216,106,.045),rgba(22,141,255,.018));
        box-shadow:inset 0 1px 0 rgba(255,255,255,.025);
        overflow:hidden;
    }
    .public-footer-accordion + .public-footer-accordion{margin-top:10px}
    .public-footer-accordion .public-footer-summary{
        display:flex;
        align-items:center;
        justify-content:space-between;
        min-height:52px;
        margin:0;
        padding:0 14px 0 13px;
        cursor:pointer;
        pointer-events:auto;
    }
    .public-footer-accordion .public-footer-heading{
        margin:0;
        padding-left:12px;
        font-size:14px;
    }
    .public-footer-heading::before{top:3px;height:15px}
    .public-footer-summary-chevron{
        display:block!important;
        visibility:visible!important;
        width:8px;
        height:8px;
        flex:0 0 8px;
        margin-left:12px;
        border-right:1.5px solid var(--footer-green);
        border-bottom:1.5px solid var(--footer-green);
        transform:rotate(45deg) translateY(-2px);
        transition:transform .2s ease;
    }
    .public-footer-accordion[open] .public-footer-summary-chevron{
        transform:rotate(225deg) translate(-1px,-1px);
    }
    .public-footer-accordion > .public-footer-address,
    .public-footer-accordion > .public-footer-contact{
        padding:0 14px 17px;
    }
    .public-footer-address{font-size:12.5px;line-height:1.75}
    .public-footer-contact{gap:11px}
    .public-footer-contact a{font-size:12.5px;word-break:break-word}
    .public-footer-bottom{
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:7px;
        text-align:center;
        font-size:11px;
    }
    .public-footer-developer{font-size:8px}
}

@media(max-width:390px){
    .public-footer-logo{width:92px;height:92px;flex-basis:92px}
    .public-footer-brand{font-size:17px}
    .public-footer-tech{font-size:10.5px}
    .public-footer-address,
    .public-footer-contact a{font-size:12px}
    .public-footer-social{width:35px;height:35px}
}

@media(prefers-reduced-motion:reduce){
    .public-footer-contact a,
    .public-footer-social,
    .public-footer-summary-chevron{transition:none!important}
}
</style>

<footer class="public-footer{{ request()->routeIs('management') ? ' public-footer-management' : '' }}">
    <div class="public-footer-shell">
        <div class="public-footer-grid">

            <section class="public-footer-section public-footer-brand-section">
                <div class="public-footer-brand-row">
                    @if($publicFooterLogo)
                        <img
                            class="public-footer-logo"
                            src="{{ asset('storage/'.ltrim($publicFooterLogo,'/')) }}"
                            alt="{{ $publicFooterName }}"
                        >
                    @endif

                    <div class="public-footer-brand">
                        <span class="public-footer-brand-first">{{ $publicFooterNameFirst }}</span>@if($publicFooterNameRest)
                        <span class="public-footer-brand-rest">{{ $publicFooterNameRest }}</span>
                        @endif
                    </div>
                </div>

                @if($publicFooterTagline)
                    <div class="public-footer-tagline">{{ $publicFooterTagline }}</div>
                @endif

                <div class="public-footer-tech">
                    {{ $publicFooterTechnology }}
                </div>

                @if($footerVisible('social') && !empty($publicSocials))
                    <div class="public-footer-social-wrap" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a
                                class="public-footer-social"
                                style="--social-color:{{ $social['color'] }}"
                                href="{{ $social['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $social['label'] }}"
                                title="{{ $social['label'] }}"
                            >
                                <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            @if($footerVisible('columns'))
                <section class="public-footer-section">
                    <details class="public-footer-accordion" open>
                        <summary class="public-footer-summary">
                            <h2 class="public-footer-heading">
                                {{ $publicFooterOfficeHeading }}
                            </h2>
                            <span class="public-footer-summary-chevron" aria-hidden="true"></span>
                        </summary>

                        <div class="public-footer-address">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <span>{!! nl2br(e($publicFooterAddress)) !!}</span>
                        </div>
                    </details>
                </section>
            @endif

            @if($footerVisible('contact'))
                <section class="public-footer-section">
                    <details class="public-footer-accordion" open>
                        <summary class="public-footer-summary">
                            <h2 class="public-footer-heading">
                                {{ $publicFooterContactHeading }}
                            </h2>
                            <span class="public-footer-summary-chevron" aria-hidden="true"></span>
                        </summary>

                        <div class="public-footer-contact">
                            <a href="mailto:{{ $publicFooterEmail }}">
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <span>{{ $publicFooterEmail }}</span>
                            </a>

                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerSettings['phone'] ?? '+880 1712-251892') }}">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <span>{{ $publicFooterPhone }}</span>
                            </a>

                            <a href="{{ $publicFooterWebsiteUrl }}">
                                <i class="fa-solid fa-globe" aria-hidden="true"></i>
                                <span>{{ $publicFooterWebsite }}</span>
                            </a>

                            <a href="{{ $publicFooterGetInTouchUrl }}">
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                <span>{{ $publicFooterGetInTouchLabel }}</span>
                            </a>
                        </div>
                    </details>
                </section>
            @endif

        </div>

        <div class="public-footer-bottom">
            @if($footerVisible('copyright'))
                <div>
                    © {{ date('Y') }} {{ $publicFooterName }} · {{ $publicFooterCopyright }}
                </div>
            @endif

            <div class="public-footer-developer">
                Developed by
                <a href="mailto:TariqueBN@gmail.com" aria-label="Email developer Saif Al-Islam">Saif Al-Islam</a>
            </div>
        </div>
    </div>
</footer>

<script>
(function(){
    document.querySelectorAll('.public-footer-social').forEach(function(el){
        el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});
        el.addEventListener('blur',function(){el.classList.remove('is-touched')});
    });
})();
</script>

<style>
@media (max-width:850px){
    .bio-modal,
    .bio-modal.open{
        display:none !important;
        visibility:hidden !important;
        pointer-events:none !important;
    }
}
</style>

@if(request()->routeIs('home'))
<style>
/* Mobile/tablet profile viewer: identity is fixed above one independent message scroller. */
@media (max-width:1099px){
    .home-profile-modal{overflow:hidden!important;touch-action:none!important;}
    .home-profile-modal .home-profile-panel{
        display:grid!important;
        grid-template-columns:minmax(0,32%) minmax(0,68%)!important;
        grid-template-rows:auto minmax(0,1fr) auto!important;
        grid-template-areas:"photo head" "message message" "footer footer"!important;
        width:100%!important;
        height:min(90svh,720px)!important;
        max-height:calc(100svh - 24px)!important;
        min-height:0!important;
        overflow:hidden!important;
        position:relative!important;
        align-items:stretch!important;
    }
    .home-profile-modal .home-profile-info{display:contents!important;}
    .home-profile-modal .home-profile-kicker{display:none!important;}
    .home-profile-modal .home-profile-photo{
        grid-area:photo!important;
        align-self:center!important;
        justify-self:center!important;
        width:100%!important;
        max-width:100%!important;
        min-width:0!important;
        min-height:0!important;
        height:auto!important;
        padding:14px 0 14px 16px!important;
        border:0!important;
        overflow:hidden!important;
    }
    .home-profile-modal .home-profile-photo img{
        display:block!important;
        width:100%!important;
        height:auto!important;
        aspect-ratio:4 / 5!important;
        object-fit:cover!important;
    }
    .home-profile-modal .home-profile-head{
        grid-area:head!important;
        align-self:center!important;
        min-width:0!important;
        width:100%!important;
        padding:25px 58px 12px 14px!important;
        display:flex!important;
        flex-direction:column!important;
        justify-content:center!important;
    }
    .home-profile-modal .home-profile-title,
    .home-profile-modal .home-profile-role,
    .home-profile-modal .home-profile-contact-mobile{position:static!important;}
    .home-profile-modal .home-profile-divider{display:none!important;}
    .home-profile-modal .home-profile-scroll{
        grid-area:message!important;
        min-width:0!important;
        min-height:0!important;
        width:calc(100% - 32px)!important;
        height:auto!important;
        max-height:none!important;
        margin:14px 16px 12px!important;
        padding:13px 13px 18px!important;
        overflow-y:auto!important;
        overflow-x:hidden!important;
        overscroll-behavior:contain!important;
        -webkit-overflow-scrolling:touch!important;
        touch-action:pan-y!important;
        position:relative!important;
        z-index:1!important;
        box-sizing:border-box!important;
        scrollbar-gutter:stable!important;
    }
    .home-profile-modal .home-profile-footer{
        grid-area:footer!important;
        min-width:0!important;
        min-height:0!important;
        width:100%!important;
        position:relative!important;
        z-index:3!important;
        padding:0 16px calc(12px + env(safe-area-inset-bottom))!important;
    }
}
@media (max-width:650px){
    .home-profile-modal{padding:12px!important;align-items:center!important;}
    .home-profile-modal .home-profile-panel{height:min(90svh,720px)!important;max-height:calc(100svh - 24px)!important;border-radius:22px!important;}
    .home-profile-modal .home-profile-photo{padding:14px 0 14px 16px!important;}
    .home-profile-modal .home-profile-head{padding:25px 58px 12px 14px!important;}
    .home-profile-modal .home-profile-scroll{width:calc(100% - 32px)!important;margin:14px 16px 12px!important;padding:13px 13px 18px!important;}
    .home-profile-modal .home-profile-footer{padding:0 16px calc(12px + env(safe-area-inset-bottom))!important;}
}
@media (min-width:651px) and (max-width:1099px){
    .home-profile-modal .home-profile-panel{width:min(92vw,900px)!important;height:min(86svh,760px)!important;max-height:calc(100svh - 40px)!important;border-radius:24px!important;}
    .home-profile-modal .home-profile-photo{padding:18px 0 18px 20px!important;}
    .home-profile-modal .home-profile-head{padding:28px 64px 16px 18px!important;}
    .home-profile-modal .home-profile-scroll{width:calc(100% - 40px)!important;margin:16px 20px 14px!important;padding:16px 16px 20px!important;}
    .home-profile-modal .home-profile-footer{padding:0 20px 18px!important;}
}
</style>
@endif