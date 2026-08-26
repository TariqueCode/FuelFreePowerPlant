@php
    $publicBrand = $brand ?? [];
    $publicFooterName = is_object($publicBrand) ? ($publicBrand->get('name') ?: $publicBrand->get('company.name') ?: config('fuelfree.company.name')) : ($publicBrand['name'] ?? $publicBrand['company.name'] ?? config('fuelfree.company.name'));
@endphp
<style>.public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.15);padding:30px 0 45px;color:#688893;font-size:9px}</style>
<footer class="public-footer"><div class="shell">© {{ date('Y') }} {{ $publicFooterName }} · All rights reserved.</div></footer>
