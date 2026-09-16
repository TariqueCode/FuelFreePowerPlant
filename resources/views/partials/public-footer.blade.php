@php
    $publicBrand = $brand ?? [];
    $footerSettings = config('fuelfree.footer', []);
    $footerVisibility = \App\Models\SystemSetting::query()->whereIn('key',['design.footer.columns_enabled','design.footer.links_enabled','design.footer.social_enabled','design.footer.contact_enabled','design.footer.copyright_enabled'])->pluck('value','key');
    $footerVisible = fn($key) => filter_var($footerVisibility->get('design.footer.'.$key.'_enabled','1'), FILTER_VALIDATE_BOOLEAN);
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicFooterTagline = $footerSettings['tagline'] ?? (is_object($publicBrand) ? ($publicBrand->get('tagline') ?: $publicBrand->get('company.tagline') ?: config('fuelfree.company.tagline')) : ($publicBrand['tagline'] ?? $publicBrand['company.tagline'] ?? config('fuelfree.company.tagline')));
    $publicFooterLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    $publicFooterName = "Fuel Free Power Plant Limited";
    $publicFooterNameFirst = "Fuel Free";
    $publicFooterNameRest = "Power Plant Limited";
@endphp
@php
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#39E6A6')])->values()->all());
@endphp

<style>
.public-footer{
    margin-top:60px;
    border-top:1px solid rgba(57,230,166,.18);
    padding:52px 0 24px;
    color:#8caab5;
    font-size:14px;
    line-height:1.7;
    background:
        radial-gradient(circle at 15% 20%,rgba(57,230,166,.055),transparent 34%),
        linear-gradient(180deg,rgba(6,25,35,.22),rgba(2,11,18,.52));
    overflow:hidden;
}
.public-footer-management{margin-top:28px}
.public-footer-shell{width:min(1120px,calc(100% - 40px));margin:0 auto}
.public-footer-grid{
    display:grid;
    grid-template-columns:minmax(0,1.15fr) minmax(0,1fr) minmax(0,1fr);
    gap:34px;
    padding-bottom:34px;
}
.public-footer-section{min-width:0}
.public-footer-brand-section{
    padding-right:20px;
    position:relative;
}
.public-footer-brand-section::after{
    content:"";
    position:absolute;
    top:4px;
    right:0;
    width:1px;
    height:calc(100% - 8px);
    background:linear-gradient(
        180deg,
        transparent,
        rgba(57,230,166,.28),
        rgba(255,255,255,.12),
        transparent
    );
}
.public-footer-brand-row{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:13px;
}
.public-footer-logo{
    width:124px;
    height:124px;
    object-fit:contain;
    object-position:center;
    display:block;
    flex:0 0 124px;
    filter:drop-shadow(0 0 18px rgba(57,230,166,.20));
}
.public-footer-brand{
    color:#fff;
    font-size:23px;
    font-weight:850;
    line-height:1.12;
    letter-spacing:-.35px;
    max-width:180px;
}
.public-footer-brand-first,.public-footer-brand-middle,.public-footer-brand-rest{
    display:block;
}
.public-footer-brand-first{
    background:linear-gradient(90deg,#39e6a6 0%,#8df2ce 48%,#fff 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}
.public-footer-brand-rest{
    background:linear-gradient(90deg,#b9f8df 0%,#fff 75%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}
.public-footer-tagline{
    color:#62e8c0;
    font-size:13px;
    font-weight:750;
    margin:0 0 7px;
}
.public-footer-tech{
    color:#7899a5;
    font-size:12px;
    line-height:1.7;
    max-width:360px;
}
.public-footer-heading{
    position:relative;
    margin:2px 0 17px;
    padding-left:13px;
    color:#effcff;
    font-size:15px;
    font-weight:800;
    letter-spacing:.15px;
}
.public-footer-heading::before{
    content:"";
    position:absolute;
    left:0;
    top:4px;
    width:3px;
    height:16px;
    border-radius:3px;
    background:linear-gradient(180deg,#39e6a6,#fff);
}
.public-footer-address{
    display:flex;
    gap:11px;
    align-items:flex-start;
    color:#8caab5;
    font-size:13px;
    line-height:1.8;
}
.public-footer-address span{
    max-width:230px;
}
.public-footer-address i{
    color:#39e6a6;
    width:16px;
    margin-top:5px;
    flex:0 0 16px;
    text-align:center;
}
.public-footer-contact{display:grid;gap:12px}
.public-footer-contact a{
    display:flex;
    align-items:flex-start;
    gap:11px;
    color:#8caab5;
    text-decoration:none;
    font-size:13px;
    line-height:1.55;
    transition:color .18s ease,transform .18s ease;
}
.public-footer-contact a:hover,
.public-footer-contact a:focus-visible{
    color:#fff;
    transform:translateX(2px);
}
.public-footer-contact i{
    width:18px;
    height:18px;
    display:inline-grid;
    place-items:center;
    color:#39e6a6;
    margin-top:2px;
    text-align:center;
    flex:0 0 18px;
    font-family:"Font Awesome 6 Free"!important;
    font-weight:900!important;
    font-style:normal;
    line-height:1;
}
.public-footer-social-wrap{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    gap:9px;
    flex-wrap:wrap;
    margin-top:20px;
}
.public-footer-social{
    --social-color:#39e6a6;
    width:38px;
    height:38px;
    display:grid;
    place-items:center;
    border:1px solid rgba(57,230,166,.20);
    border-radius:11px;
    background:linear-gradient(
        135deg,
        rgba(57,230,166,.055),
        rgba(255,255,255,.025)
    );
    color:#7899a5;
    text-decoration:none;
    transition:
        color .18s ease,
        background .18s ease,
        border-color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}
.public-footer-social:hover,
.public-footer-social:focus-visible,
.public-footer-social.is-touched{
    color:var(--social-color);
    background:linear-gradient(
        135deg,
        rgba(57,230,166,.12),
        rgba(255,255,255,.055)
    );
    border-color:rgba(57,230,166,.48);
    transform:translateY(-2px);
    box-shadow:
        0 8px 24px rgba(0,0,0,.18),
        0 0 18px rgba(57,230,166,.10);
}
.public-footer-social i{font-size:14px}

.public-footer-accordion{margin:0}
.public-footer-summary{
    list-style:none;
    cursor:default;
    color:#effcff;
}
.public-footer-summary::-webkit-details-marker{display:none}
.public-footer-summary .public-footer-summary-chevron{display:none}

.public-footer-bottom{
    border-top:1px solid rgba(57,230,166,.12);
    padding-top:17px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    color:#607e89;
    font-size:12px;
}
.public-footer-developer{
    font-size:9px;
    line-height:1.4;
    color:#496b75;
    white-space:nowrap;
}
.public-footer-developer a{
    color:#547f8a;
    text-decoration:none;
    transition:color .18s ease;
}
.public-footer-developer a:hover,
.public-footer-developer a:focus-visible{
    color:#79aebb;
}

@media(max-width:900px){
    .public-footer-shell{
        width:min(100% - 32px,760px);
    }

    .public-footer-grid{
        grid-template-columns:minmax(0,1fr) minmax(0,1fr);
        gap:28px 26px;
    }

    .public-footer-brand-section{
        grid-column:1 / -1;
        padding-right:0;
        padding-bottom:24px;
        border-bottom:1px solid rgba(57,230,166,.12);
    }

    .public-footer-brand-section::after{display:none}

    .public-footer-logo{
        width:112px;
        height:112px;
        flex-basis:112px;
    }

    .public-footer-brand{font-size:23px}

    .public-footer-accordion .public-footer-summary{
        pointer-events:none;
    }
}

@media(max-width:600px){
    .public-footer{
        margin-top:44px;
        padding:38px 0 20px;
    }

    .public-footer-management{
        margin-top:24px;
        padding:30px 0 18px;
    }

    .public-footer-shell{
        width:calc(100% - 24px);
    }

    .public-footer-grid{
        grid-template-columns:1fr;
        gap:12px;
        padding-bottom:28px;
    }

    .public-footer-brand-section{
        grid-column:auto;
        padding:0 0 25px;
        text-align:center;
        display:flex;
        flex-direction:column;
        align-items:center;
    }

    .public-footer-brand-row{
        flex-direction:column;
        justify-content:center;
        gap:10px;
        margin-bottom:10px;
    }

    .public-footer-logo{
        width:104px;
        height:104px;
        flex-basis:104px;
    }

      .public-footer-brand{width:100%;max-width:none;white-space:nowrap;font-size:clamp(14px,4.5vw,19px);letter-spacing:-.25px;text-align:center;background:linear-gradient(270deg,#fff 0%,#39E6A6 100%);-webkit-background-clip:text;background-clip:text;color:transparent}

    .public-footer-tagline{
        font-size:11px;
        margin-bottom:5px;
    }

    .public-footer-tech{
        max-width:310px;
        font-size:11px;
    }

    .public-footer-social-wrap{
        justify-content:center;
        margin-top:16px;
    }

    .public-footer-section:not(.public-footer-brand-section){
        width:100%;
    }

    .public-footer-accordion{
        border:1px solid rgba(57,230,166,.15);
        border-radius:13px;
        background:linear-gradient(
            135deg,
            rgba(57,230,166,.035),
            rgba(255,255,255,.012)
        );
        overflow:hidden;
    }

    .public-footer-accordion + .public-footer-accordion{
        margin-top:10px;
    }

    .public-footer-accordion .public-footer-summary{
        display:flex;
        align-items:center;
        justify-content:space-between;
        min-height:50px;
        margin:0;
        padding:0 14px 0 13px;
        cursor:pointer;
    }

    .public-footer-accordion .public-footer-heading{
        margin:0;
        padding-left:12px;
        font-size:14px;
    }

    .public-footer-accordion .public-footer-heading::before{
        top:3px;
        height:15px;
    }

    .public-footer-summary-chevron{
        display:block!important;
        width:8px;
        height:8px;
        border-right:1.5px solid #39e6a6;
        border-bottom:1.5px solid #39e6a6;
        transform:rotate(45deg) translateY(-2px);
        transition:transform .18s ease;
        flex:0 0 8px;
        margin-left:12px;
    }

    .public-footer-accordion[open] .public-footer-summary-chevron{
        transform:rotate(225deg) translate(-1px,-1px);
    }

    .public-footer-accordion > .public-footer-address,
    .public-footer-accordion > .public-footer-contact{
        padding:0 14px 16px;
    }

    .public-footer-address{
        font-size:12.5px;
        line-height:1.75;
    }

    .public-footer-contact{
        gap:11px;
    }

    .public-footer-contact a{
        font-size:12.5px;
        word-break:break-word;
    }

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
    .public-footer-logo{
        width:94px;
        height:94px;
        flex-basis:94px;
    }

    .public-footer-brand{font-size:18px}
    .public-footer-tech{font-size:10.5px}

    .public-footer-address,
    .public-footer-contact a{
        font-size:12px;
    }

    .public-footer-social{
        width:34px;
        height:34px;
    }
}

@media(prefers-reduced-motion:reduce){
    .public-footer-contact a,
    .public-footer-social,
    .public-footer-summary-chevron{
        transition:none!important;
    }
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
                    {{ $footerSettings['technology'] ?? 'Fuel-Free Flywheel-Based Clean Energy Technology' }}
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
                                {{ $footerSettings['office_heading'] ?? 'Office' }}
                            </h2>
                            <span class="public-footer-summary-chevron" aria-hidden="true"></span>
                        </summary>

                        <div class="public-footer-address">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <span>{!! nl2br(e($footerSettings['address'] ?? 'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh')) !!}</span>
                        </div>
                    </details>
                </section>
            @endif

            @if($footerVisible('contact'))
                <section class="public-footer-section">
                    <details class="public-footer-accordion" open>
                        <summary class="public-footer-summary">
                            <h2 class="public-footer-heading">
                                {{ $footerSettings['contact_heading'] ?? 'Contact' }}
                            </h2>
                            <span class="public-footer-summary-chevron" aria-hidden="true"></span>
                        </summary>

                        <div class="public-footer-contact">
                            <a href="mailto:{{ $footerSettings['email'] ?? 'info@fuelfreepowerplant.com' }}">
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <span>{{ $footerSettings['email'] ?? 'info@fuelfreepowerplant.com' }}</span>
                            </a>

                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerSettings['phone'] ?? '+880 1712-251892') }}">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <span>{{ $footerSettings['phone'] ?? '+880 1712-251892' }}</span>
                            </a>

                            <a href="{{ $footerSettings['website_url'] ?? 'https://www.fuelfreepowerplant.com' }}">
                                <i class="fa-solid fa-globe" aria-hidden="true"></i>
                                <span>{{ $footerSettings['website'] ?? 'www.fuelfreepowerplant.com' }}</span>
                            </a>

                            <a href="{{ $footerSettings['get_in_touch_url'] ?? route('contact') }}">
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                <span>{{ $footerSettings['get_in_touch_label'] ?? 'Get in touch' }}</span>
                            </a>
                        </div>
                    </details>
                </section>
            @endif

        </div>

        <div class="public-footer-bottom">
            @if($footerVisible('copyright'))
                <div>
                    © {{ date('Y') }} {{ $publicFooterName }} · {{ $footerSettings['copyright_text'] ?? 'All rights reserved.' }}
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