@php($footerName = \App\Models\SystemSetting::query()->where('key','company.name')->value('value') ?: config('fuelfree.company.name'))
<style>.footer{margin-top:60px;border-top:1px solid var(--line);padding:30px 0 45px;color:#688893;font-size:9px}</style>
<footer class="footer"><div class="shell">© {{ date('Y') }} {{ $footerName }} · All rights reserved.</div></footer>
