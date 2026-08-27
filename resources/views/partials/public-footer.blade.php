@php
    $publicBrand = $brand ?? [];
    $publicSettings = $settings ?? \App\Models\SystemSetting::query()->pluck('value','key');
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
    $publicTagline = $publicSettings->get('company.tagline', config('fuelfree.company.tagline'));
    $publicDescription = $publicSettings->get('company.description', 'Fuel-Free Flywheel-Based Clean Energy Technology');
    $publicAddress = $publicSettings->get('company.address', '');
    $publicPhone = $publicSettings->get('company.phone', '');
    $publicEmail = $publicSettings->get('company.email', '');
    $publicWebsite = $publicSettings->get('company.website', $publicSettings->get('company.domain', ''));
    $footerEnabled = filter_var($publicSettings->get('footer.enabled','1'), FILTER_VALIDATE_BOOLEAN);
    $showCompany = filter_var($publicSettings->get('footer.show_company','1'), FILTER_VALIDATE_BOOLEAN);
    $showContact = filter_var($publicSettings->get('footer.show_contact','1'), FILTER_VALIDATE_BOOLEAN);
    $showSocial = filter_var($publicSettings->get('footer.show_social','1'), FILTER_VALIDATE_BOOLEAN);
    $copyrightText = trim($publicSettings->get('footer.copyright','All rights reserved.'));
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
@endphp
@if($footerEnabled)
<style>
.public-footer{margin-top:64px;border-top:1px solid rgba(86,210,238,.15);padding:42px 0 22px;color:#8aa8b1;font-size:14px;line-height:1.7;background:linear-gradient(180deg,rgba(3,16,24,.2),rgba(2,10,16,.6))}
.public-footer-shell{width:min(1180px,calc(100% - 28px));margin:auto}
.public-footer-grid{display:grid;grid-template-columns:1.35fr .85fr .9fr;gap:34px;padding-bottom:30px}
.public-footer-col h3{margin:0 0 11px;color:#eaf9fc;font-size:15px;font-weight:800}
.public-footer-company{display:flex;gap:13px;align-items:flex-start}
.public-footer-logo{width:48px;height:48px;flex:0 0 48px;border-radius:12px;object-fit:contain;border:1px solid rgba(86,210,238,.14);background:#061923;padding:4px}
.public-footer-logo-fallback{width:48px;height:48px;flex:0 0 48px;display:grid;place-items:center;border-radius:12px;color:#51d8f0;border:1px solid rgba(86,210,238,.14);background:#061923}
.public-footer-company strong{display:block;color:#eaf9fc;font-size:16px;line-height:1.3}.public-footer-tagline{display:block;margin-top:4px;color:#5fcde5;font-size:10px;font-weight:700}.public-footer-description{margin:10px 0 0;color:#7897a1;font-size:12px;line-height:1.65}
.public-footer-links{display:grid;gap:7px}.public-footer-links a{color:#8ba9b3;text-decoration:none;transition:.18s}.public-footer-links a:hover{color:#7ce6f7;transform:translateX(2px)}
.public-footer-contact{display:grid;gap:9px}.public-footer-contact a,.public-footer-contact span{display:flex;gap:9px;align-items:flex-start;color:#8ba9b3;text-decoration:none}.public-footer-contact i{width:16px;margin-top:4px;color:#51d8f0;text-align:center}.public-footer-contact a:hover{color:#dff9fd}
.public-footer-social{display:flex;align-items:center;justify-content:flex-start;flex-wrap:wrap;gap:9px;margin-top:12px}.public-footer-social a{--social-color:#51d8f0;width:40px;height:40px;display:grid;place-items:center;border:1px solid rgba(86,210,238,.16);border-radius:11px;background:rgba(67,209,240,.045);color:#86a7b1;text-decoration:none;transition:.2s ease}.public-footer-social a:hover,.public-footer-social a:focus-visible,.public-footer-social a.is-touched{color:var(--social-color);background:color-mix(in srgb,var(--social-color) 10%,transparent);border-color:color-mix(in srgb,var(--social-color) 42%,transparent);box-shadow:0 0 18px color-mix(in srgb,var(--social-color) 18%,transparent);transform:translateY(-2px)}.public-footer-social i{font-size:15px}
.public-footer-bottom{border-top:1px solid rgba(86,210,238,.09);padding-top:17px;display:flex;justify-content:space-between;align-items:center;gap:15px;color:#607e88;font-size:11px}.public-footer-bottom a{color:#6e9ba7;text-decoration:none}.public-footer-bottom a:hover{color:#9eeaf5}
@media(max-width:800px){.public-footer-grid{grid-template-columns:1fr 1fr;gap:28px}.public-footer-company-col{grid-column:1/-1}}
@media(max-width:520px){.public-footer{margin-top:45px;padding:32px 0 18px}.public-footer-grid{grid-template-columns:1fr;gap:24px}.public-footer-company-col{grid-column:auto}.public-footer-bottom{flex-direction:column;align-items:flex-start;font-size:10px}.public-footer-description{font-size:12px}}
</style>
<footer class="public-footer">
    <div class="public-footer-shell">
        <div class="public-footer-grid">
            @if($showCompany)
            <div class="public-footer-col public-footer-company-col">
                <div class="public-footer-company">
                    @php $footerLogo = is_object($publicBrand) ? ($publicBrand->get('logo_path') ?: $publicBrand->get('company.logo_path')) : ($publicBrand['logo_path'] ?? $publicBrand['company.logo_path'] ?? null); @endphp
                    @if($footerLogo)<img class="public-footer-logo" src="{{ asset('storage/'.ltrim($footerLogo,'/')) }}" alt="{{ $publicFooterName }}">@else<span class="public-footer-logo-fallback">⚡</span>@endif
                    <div>
                        <strong>{{ $publicFooterName }}</strong>
                        @if($publicTagline)<span class="public-footer-tagline">{{ $publicTagline }}</span>@endif
                        @if($publicDescription)<p class="public-footer-description">{{ $publicDescription }}</p>@endif
                    </div>
                </div>
            </div>
            @endif

            <div class="public-footer-col">
                <h3>Quick Links</h3>
                <div class="public-footer-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('site.about') }}">About Us</a>
                    <a href="{{ route('site.gallery') }}">Gallery</a>
                    <a href="{{ route('news.index') }}">News &amp; Notices</a>
                    <a href="{{ route('site.career') }}">Career</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>

            @if($showContact)
            <div class="public-footer-col">
                <h3>Contact</h3>
                <div class="public-footer-contact">
                    @if($publicAddress)<span><i class="fa-solid fa-location-dot"></i><span>{{ $publicAddress }}</span></span>@endif
                    @if($publicPhone)<a href="tel:{{ preg_replace('/[^0-9+]/','',$publicPhone) }}"><i class="fa-solid fa-phone"></i><span>{{ $publicPhone }}</span></a>@endif
                    @if($publicEmail)<a href="mailto:{{ $publicEmail }}"><i class="fa-solid fa-envelope"></i><span>{{ $publicEmail }}</span></a>@endif
                    @if($publicWebsite)<a href="{{ str_starts_with($publicWebsite,'http') ? $publicWebsite : 'https://'.$publicWebsite }}" target="_blank" rel="noopener"><i class="fa-solid fa-globe"></i><span>{{ $publicWebsite }}</span></a>@endif
                </div>
                @if($showSocial && !empty($publicSocials))
                    <div class="public-footer-social" aria-label="Social media">
                        @foreach($publicSocials as $social)
                            <a style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"><i class="{{ $social['icon'] ?: 'fa-solid fa-link' }}" aria-hidden="true"></i></a>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
        </div>

        <div class="public-footer-bottom">
            <span>© {{ date('Y') }} {{ $publicFooterName }} · {{ $copyrightText }}</span>
            <a href="{{ route('contact') }}">Get in touch <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</footer>
<script>(function(){document.querySelectorAll('.public-footer-social a').forEach(function(el){el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});el.addEventListener('blur',function(){el.classList.remove('is-touched')});});})();</script>
@endif
