@php
    $publicBrand = $brand ?? [];
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicFooterTagline = is_object($publicBrand) ? ($publicBrand->get('tagline') ?: $publicBrand->get('company.tagline') ?: config('fuelfree.company.tagline')) : ($publicBrand['tagline'] ?? $publicBrand['company.tagline'] ?? config('fuelfree.company.tagline'));
    $publicFooterLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null);
@endphp
@php
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
@endphp
<style>
.public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.15);padding:38px 0 26px;color:#8aa8b1;font-size:14px;line-height:1.7}
.public-footer-shell{width:min(1180px,calc(100% - 28px));margin:auto}
.public-footer-grid{display:grid;grid-template-columns:1fr 1fr;gap:30px 70px;padding-bottom:30px}.public-footer-grid>section:first-child{grid-column:1/-1;padding-bottom:6px}
.public-footer-title{margin:0 0 12px;color:#eaf8fb;font-size:15px;font-weight:800}
.public-footer-brand-row{display:flex;align-items:center;gap:10px;margin-bottom:7px}
.public-footer-logo{width:34px;height:34px;object-fit:contain;flex:0 0 34px}
.public-footer-brand{color:#effcff;font-size:18px;font-weight:800;line-height:1.3}
.public-footer-tagline{color:#5fcde5;font-size:12px;font-weight:700;margin-bottom:8px}
.public-footer-tech{color:#7899a5;font-size:12px;line-height:1.7}
.public-footer-address{display:flex;gap:10px;align-items:flex-start;color:#8aa8b1;font-size:13px;line-height:1.75}
.public-footer-address i{color:#51d8f0;margin-top:4px;flex:0 0 auto}
.public-footer-contact{display:grid;gap:10px;width:100%;max-width:360px}
.public-footer-contact a{display:flex;align-items:flex-start;gap:10px;color:#8aa8b1;text-decoration:none;font-size:13px;line-height:1.5}
.public-footer-contact a:hover{color:#effcff}
.public-footer-contact i{width:16px;color:#51d8f0;margin-top:2px;text-align:center;flex:0 0 16px}
.public-footer-social-wrap{display:flex;align-items:center;justify-content:flex-start;gap:10px;flex-wrap:wrap;margin-top:18px}
.public-footer-social{--social-color:#51d8f0;width:40px;height:40px;display:grid;place-items:center;border:1px solid rgba(86,210,238,.16);border-radius:11px;background:rgba(67,209,240,.045);color:#86a7b1;text-decoration:none;transition:.2s ease}
.public-footer-social:hover,.public-footer-social:focus-visible,.public-footer-social.is-touched{color:var(--social-color);background:color-mix(in srgb,var(--social-color) 10%,transparent);border-color:color-mix(in srgb,var(--social-color) 42%,transparent);box-shadow:0 0 18px color-mix(in srgb,var(--social-color) 18%,transparent);transform:translateY(-2px)}
.public-footer-social i{font-size:15px}
.public-footer-bottom{border-top:1px solid rgba(86,210,238,.11);padding-top:18px;display:flex;align-items:center;justify-content:space-between;gap:16px;color:#607e89;font-size:12px}
.public-footer-bottom a{color:#72b9c9;text-decoration:none}
.public-footer-bottom a:hover{color:#effcff}
.public-footer-developer{font-size:9px;line-height:1.4;color:#496b75;white-space:nowrap}
.public-footer-developer a{color:#547f8a;text-decoration:none}
.public-footer-developer a:hover{color:#79aebb}
@media(max-width:760px){
    .public-footer{margin-top:45px;padding-top:30px}
    .public-footer-grid{grid-template-columns:1fr 1fr;gap:28px 14px;padding-bottom:24px;text-align:center}
    .public-footer-grid>section{display:flex;flex-direction:column;align-items:center}.public-footer-grid>section:first-child{grid-column:1/-1;padding-bottom:6px}.public-footer-grid>section:nth-child(2),.public-footer-grid>section:nth-child(3){width:100%}
    .public-footer-brand-row{justify-content:center}
    .public-footer-tech{max-width:290px}
    .public-footer-address{justify-content:center;text-align:center}
    .public-footer-contact{justify-items:center}
    .public-footer-contact a{justify-content:center;text-align:center}
    .public-footer-social-wrap{justify-content:center;margin-top:14px}
    .public-footer-bottom{flex-direction:column;align-items:center;justify-content:center;gap:8px;text-align:center}.public-footer-address{max-width:250px}
    .public-footer-developer{font-size:8px}
}
</style>
<footer class="public-footer">
    <div class="public-footer-shell">
        <div class="public-footer-grid">
            <section>
                <div class="public-footer-brand-row">
                    @if($publicFooterLogo)
                        <img class="public-footer-logo" src="{{ asset('storage/'.$publicFooterLogo) }}" alt="{{ $publicFooterName }}">
                    @endif
                    <div class="public-footer-brand">{{ $publicFooterName }}</div>
                </div>
                @if($publicFooterTagline)<div class="public-footer-tagline">{{ $publicFooterTagline }}</div>@endif
                <div class="public-footer-tech">Fuel-Free Flywheel-Based Clean Energy Technology</div>
                @if(!empty($publicSocials))
                    <div class="public-footer-social-wrap" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a class="public-footer-social" style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <h2 class="public-footer-title">Office</h2>
                <div class="public-footer-address">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>House-141, 3rd Floor, Road-22,<br>Mohakhali DOHS, Dhaka-1206,<br>Bangladesh</span>
                </div>
            </section>

            <section>
                <h2 class="public-footer-title">Contact</h2>
                <div class="public-footer-contact">
                    <a href="mailto:info@fuelfreepowerplant.com"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>info@fuelfreepowerplant.com</span></a>
                    <a href="tel:+8801712251892"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>+880 1712-251892</span></a>
                    <a href="https://www.fuelfreepowerplant.com"><i class="fa-solid fa-globe" aria-hidden="true"></i><span>www.fuelfreepowerplant.com</span></a>
                    <a href="{{ route('contact') }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><span>Get in touch</span></a>
                </div>
            </section>
        </div>

        <div class="public-footer-bottom">
            <div>© {{ date('Y') }} {{ $publicFooterName }} · All rights reserved.</div>
            <div class="public-footer-developer">Developed by <a href="mailto:TariqueBN@gmail.com" aria-label="Email developer Saif Al-Islam">Saif Al-Islam</a></div>
        </div>
    </div>
</footer>

<script>(function(){document.querySelectorAll('.public-footer-social').forEach(function(el){el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});el.addEventListener('blur',function(){el.classList.remove('is-touched')});});})();</script>