@php
    $publicBrand = $brand ?? [];
    $footerSettings = config('fuelfree.footer', []);
    $footerVisibility = \App\Models\SystemSetting::query()->whereIn('key',['design.footer.columns_enabled','design.footer.links_enabled','design.footer.social_enabled','design.footer.contact_enabled','design.footer.copyright_enabled'])->pluck('value','key');
    $footerVisible = fn($key) => filter_var($footerVisibility->get('design.footer.'.$key.'_enabled','1'), FILTER_VALIDATE_BOOLEAN);
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicFooterTagline = $footerSettings['tagline'] ?? (is_object($publicBrand) ? ($publicBrand->get('tagline') ?: $publicBrand->get('company.tagline') ?: config('fuelfree.company.tagline')) : ($publicBrand['tagline'] ?? $publicBrand['company.tagline'] ?? config('fuelfree.company.tagline')));
    $publicFooterLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
    $publicFooterNameParts = preg_split('/\s+/', trim((string) $publicFooterName), 2);
    $publicFooterNameFirst = $publicFooterNameParts[0] ?? '';
    $publicFooterNameRest = $publicFooterNameParts[1] ?? '';
@endphp
@php
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
@endphp

<style>
.public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.12);padding:46px 0 24px;color:#8aa8b1;font-size:14px;line-height:1.7}
.public-footer-shell{width:min(1120px,calc(100% - 40px));margin:0 auto}
.public-footer-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,1fr) minmax(0,1fr);gap:46px;padding-bottom:38px}
.public-footer-section{min-width:0}
.public-footer-brand-section{padding-right:20px}
.public-footer-brand-row{display:flex;align-items:center;gap:11px;margin-bottom:9px}
.public-footer-logo{width:38px;height:38px;object-fit:contain;flex:0 0 38px}
.public-footer-brand{color:#effcff;font-size:20px;font-weight:800;line-height:1.25;letter-spacing:-.2px}.public-footer-brand-first{color:#51d8f0}.public-footer-brand-rest{color:#effcff}
.public-footer-tagline{color:#5fcde5;font-size:12px;font-weight:700;margin:0 0 6px 49px}
.public-footer-tech{color:#7899a5;font-size:12px;line-height:1.7;max-width:300px}
.public-footer-heading{position:relative;margin:2px 0 17px;padding-left:12px;color:#eaf8fb;font-size:14px;font-weight:800;letter-spacing:.2px}
.public-footer-heading::before{content:"";position:absolute;left:0;top:4px;width:3px;height:14px;border-radius:3px;background:#51d8f0}
.public-footer-address{display:flex;gap:11px;align-items:flex-start;color:#8aa8b1;font-size:13px;line-height:1.8}
.public-footer-address i{color:#51d8f0;width:16px;margin-top:5px;flex:0 0 16px;text-align:center}
.public-footer-contact{display:grid;gap:12px}
.public-footer-contact a{display:flex;align-items:flex-start;gap:11px;color:#8aa8b1;text-decoration:none;font-size:13px;line-height:1.55;transition:color .2s ease,transform .2s ease}
.public-footer-contact a:hover,.public-footer-contact a:focus-visible{color:#effcff;transform:translateX(2px)}
.public-footer-contact i{width:16px;color:#51d8f0;margin-top:3px;text-align:center;flex:0 0 16px}
.public-footer-social-wrap{display:flex;align-items:center;justify-content:flex-start;gap:9px;flex-wrap:wrap;margin-top:20px}
.public-footer-social{--social-color:#51d8f0;width:36px;height:36px;display:grid;place-items:center;border:1px solid rgba(86,210,238,.14);border-radius:10px;background:rgba(67,209,240,.035);color:#7899a5;text-decoration:none;transition:.2s ease}
.public-footer-social:hover,.public-footer-social:focus-visible,.public-footer-social.is-touched{color:var(--social-color);background:rgba(67,209,240,.07);border-color:rgba(86,210,238,.35);transform:translateY(-2px)}
.public-footer-social i{font-size:14px}
.public-footer-bottom{border-top:1px solid rgba(86,210,238,.10);padding-top:17px;display:flex;align-items:center;justify-content:space-between;gap:18px;color:#607e89;font-size:12px}
.public-footer-developer{font-size:9px;line-height:1.4;color:#496b75;white-space:nowrap}
.public-footer-developer a{color:#547f8a;text-decoration:none;transition:color .2s ease}
.public-footer-developer a:hover,.public-footer-developer a:focus-visible{color:#79aebb}
@media(max-width:760px){
    .public-footer{margin-top:44px;padding:34px 0 20px}
    .public-footer-shell{width:min(100% - 32px,560px)}
    .public-footer-grid{grid-template-columns:1fr 1fr;gap:34px 22px;padding-bottom:28px}
    .public-footer-brand-section{grid-column:1/-1;padding:0;text-align:center;display:flex;flex-direction:column;align-items:center}
    .public-footer-brand-row{justify-content:center;margin-bottom:8px}
    .public-footer-logo{width:36px;height:36px;flex-basis:36px}
    .public-footer-brand{font-size:19px}
    .public-footer-tagline{margin:0 0 5px;font-size:11px}
    .public-footer-tech{max-width:290px;font-size:11px}
    .public-footer-social-wrap{justify-content:center;margin-top:15px}
    .public-footer-section:not(.public-footer-brand-section){width:100%;text-align:left}
    .public-footer-heading{margin-bottom:15px;font-size:14px}
    .public-footer-address{justify-content:flex-start;text-align:left;font-size:12.5px;line-height:1.75}
    .public-footer-contact{justify-items:stretch;gap:11px}
    .public-footer-contact a{justify-content:flex-start;text-align:left;font-size:12.5px;word-break:break-word}
    .public-footer-bottom{flex-direction:column;align-items:center;justify-content:center;gap:7px;text-align:center;font-size:11px}
    .public-footer-developer{font-size:8px}
}
@media(max-width:390px){
    .public-footer-shell{width:calc(100% - 24px)}
    .public-footer-grid{gap:30px 14px}
    .public-footer-brand{font-size:18px}
    .public-footer-address,.public-footer-contact a{font-size:12px}
}
</style>

<footer class="public-footer">
    <div class="public-footer-shell">
        <div class="public-footer-grid">
            <section class="public-footer-section public-footer-brand-section">
                <div class="public-footer-brand-row">
                    @if($publicFooterLogo)
                        <img class="public-footer-logo" src="{{ asset('storage/'.$publicFooterLogo) }}" alt="{{ $publicFooterName }}">
                    @endif
                    <div class="public-footer-brand"><span class="public-footer-brand-first">{{ $publicFooterNameFirst }}</span>@if($publicFooterNameRest) <span class="public-footer-brand-rest">{{ $publicFooterNameRest }}</span>@endif</div>
                </div>
                @if($publicFooterTagline)
                    <div class="public-footer-tagline">{{ $publicFooterTagline }}</div>
                @endif
                <div class="public-footer-tech">{{ $footerSettings['technology'] ?? 'Fuel-Free Flywheel-Based Clean Energy Technology' }}</div>
                @if($footerVisible('social') && !empty($publicSocials))
                    <div class="public-footer-social-wrap" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a class="public-footer-social" style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            @if($footerVisible('columns'))<section class="public-footer-section">
                <h2 class="public-footer-heading">{{ $footerSettings['office_heading'] ?? 'Office' }}</h2>
                <div class="public-footer-address">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>{!! nl2br(e($footerSettings['address'] ?? 'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh')) !!}</span>
                </div>
            </section>@endif

            @if($footerVisible('contact'))<section class="public-footer-section">
                <h2 class="public-footer-heading">{{ $footerSettings['contact_heading'] ?? 'Contact' }}</h2>
                <div class="public-footer-contact">
                    <a href="mailto:{{ $footerSettings['email'] ?? 'info@fuelfreepowerplant.com' }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>{{ $footerSettings['email'] ?? 'info@fuelfreepowerplant.com' }}</span></a>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerSettings['phone'] ?? '+880 1712-251892') }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>{{ $footerSettings['phone'] ?? '+880 1712-251892' }}</span></a>
                    <a href="{{ $footerSettings['website_url'] ?? 'https://www.fuelfreepowerplant.com' }}"><i class="fa-solid fa-globe" aria-hidden="true"></i><span>{{ $footerSettings['website'] ?? 'www.fuelfreepowerplant.com' }}</span></a>
                    <a href="{{ $footerSettings['get_in_touch_url'] ?? route('contact') }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><span>{{ $footerSettings['get_in_touch_label'] ?? 'Get in touch' }}</span></a>
                </div>
            </section>@endif
        </div>

        <div class="public-footer-bottom">
            @if($footerVisible('copyright'))<div>© {{ date('Y') }} {{ $publicFooterName }} · {{ $footerSettings['copyright_text'] ?? 'All rights reserved.' }}</div>@endif
            <div class="public-footer-developer">Developed by <a href="mailto:TariqueBN@gmail.com" aria-label="Email developer Saif Al-Islam">Saif Al-Islam</a></div>
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