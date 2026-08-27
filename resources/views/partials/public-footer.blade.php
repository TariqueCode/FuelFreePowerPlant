@php
    $publicBrand = $brand ?? [];
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
@endphp
@php
    $publicSocials = \Illuminate\Support\Facades\Cache::remember('public.social-links', 600, fn () => \App\Models\SocialLink::active()->get(['platform','label','url','icon'])->map(fn ($social) => ['platform' => $social->platform, 'label' => $social->label, 'url' => $social->url, 'icon' => $social->icon, 'color' => data_get(config('fuelfree.social.platforms'), $social->platform.'.color', '#51D8F0')])->values()->all());
@endphp
<style>
.public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.15);padding:34px 0 45px;color:#8aa8b1;font-size:14px;line-height:1.7}
.public-footer-shell{width:min(1180px,calc(100% - 28px));margin:auto}
.public-footer-social{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:10px;margin-bottom:24px}
.public-footer-social a{--social-color:#51d8f0;width:42px;height:42px;display:grid;place-items:center;border:1px solid rgba(86,210,238,.16);border-radius:12px;background:rgba(67,209,240,.045);color:#86a7b1;text-decoration:none;transition:.2s ease}
.public-footer-social a:hover,.public-footer-social a:focus-visible,.public-footer-social a.is-touched{color:var(--social-color);background:color-mix(in srgb,var(--social-color) 10%,transparent);border-color:color-mix(in srgb,var(--social-color) 42%,transparent);box-shadow:0 0 18px color-mix(in srgb,var(--social-color) 18%,transparent);transform:translateY(-2px)}
.public-footer-social i{font-size:16px}
.public-footer-follow{text-align:center;color:#5fcde5;font-size:9px;font-weight:800;letter-spacing:.2em;text-transform:uppercase;margin-bottom:11px}
.public-footer-copy{text-align:center}
@media(max-width:520px){.public-footer{font-size:14px;padding-bottom:35px}.public-footer-social a{width:40px;height:40px}}
</style>
<footer class="public-footer">
    <div class="public-footer-shell">
        @if(!empty($publicSocials))
            <div class="public-footer-follow">Follow us</div>
            <div class="public-footer-social" aria-label="Social media">
                @foreach($publicSocials as $social)
                    <a style="--social-color:{{ $social['color'] }}" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                        <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        @endif
        <div class="public-footer-copy">© {{ date('Y') }} {{ $publicFooterName }} · All rights reserved.</div>
    </div>
</footer>

<script>(function(){document.querySelectorAll('.public-footer-social a').forEach(function(el){el.addEventListener('pointerdown',function(){el.classList.add('is-touched')},{passive:true});el.addEventListener('blur',function(){el.classList.remove('is-touched')});});})();</script>
