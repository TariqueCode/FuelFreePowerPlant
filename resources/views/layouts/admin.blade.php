@php
$brand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path'])->pluck('value','key');
$brandName = $brand->get('company.name') ?: config('fuelfree.company.name');
$brandLogo = $brand->get('company.logo_path');

$groups = [
 ['label'=>'Dashboard','items'=>[
   ['label'=>'Overview','icon'=>'fa-house','route'=>'admin.dashboard','permission'=>null,'roles'=>['super-admin','administrator','project-manager']],
 ]],
 ['label'=>'Website','items'=>[
   ['label'=>'Company','icon'=>'fa-building','route'=>'admin.site-content.index','params'=>['type'=>'company'],'permission'=>'website.view'],
   ['label'=>'Management','icon'=>'fa-people-group','route'=>'admin.management.index','permission'=>'website.view'],
   ['label'=>'Slider','icon'=>'fa-panorama','route'=>'admin.sliders.index','permission'=>'website.view'],
   ['label'=>'News','icon'=>'fa-newspaper','route'=>'admin.site-content.index','params'=>['type'=>'news'],'permission'=>'website.view'],
   ['label'=>'Highlights','icon'=>'fa-bullhorn','route'=>'admin.site-popups.index','permission'=>'website.view'],
   ['label'=>'Gallery','icon'=>'fa-images','route'=>'admin.gallery.index','permission'=>'website.view'],
   ['label'=>'Social Media','icon'=>'fa-share-nodes','route'=>'admin.social-links.index','permission'=>'social-media.manage'],
 ]],
 ['label'=>'Operations','items'=>[
   ['label'=>'Power Plants','icon'=>'fa-industry','route'=>'admin.plants.index','permission'=>'plants.view'],
   ['label'=>'Performance','icon'=>'fa-chart-line','route'=>'admin.plants.performance.index','permission'=>'plants.view'],
   ['label'=>'Documents','icon'=>'fa-folder-open','route'=>'admin.documents','permission'=>'documents.view'],
 ]],
 ['label'=>'Communication','items'=>[
   ['label'=>'Help Desk','icon'=>'fa-headset','route'=>'admin.helpdesk','permission'=>'mail.view'],
   ['label'=>'Mail','icon'=>'fa-envelope','route'=>'admin.mail','permission'=>'mail.view'],
   ['label'=>'Career','icon'=>'fa-briefcase','route'=>'admin.career-applications.index','permission'=>'mail.view'],
   ['label'=>'Inquiries','icon'=>'fa-comments','route'=>'admin.inquiries.index','permission'=>'mail.view'],
 ]],
 ['label'=>'Access & Security','items'=>[
   ['label'=>'Users','icon'=>'fa-users','route'=>'admin.users.index','permission'=>'users.view'],
   ['label'=>'Audit Log','icon'=>'fa-shield-halved','route'=>'admin.audit','permission'=>'audit.view'],
   ['label'=>'System Health','icon'=>'fa-heart-pulse','route'=>'admin.health','permission'=>'system.health'],
 ]],
 ['label'=>'Configuration','items'=>[
   ['label'=>'CMS Editor','icon'=>'fa-pen-to-square','route'=>'admin.cms.index','permission'=>'website.view'],
   ['label'=>'Settings','icon'=>'fa-gear','route'=>'admin.settings','permission'=>'settings.manage'],
 ]],
];

$user = auth()->user();
$canSee = fn($item) => (!empty($item['roles']) && $user->hasRole($item['roles'])) || (empty($item['roles']) && (empty($item['permission']) || $user->hasPermission($item['permission'])));
$visibleGroups = collect($groups)->map(fn($group) => ['label'=>$group['label'],'items'=>collect($group['items'])->filter($canSee)->values()])->filter(fn($group)=>$group['items']->isNotEmpty())->values();
@endphp
@php
$theme = \App\Models\SystemSetting::query()->whereIn('key',[
 'theme.primary','theme.text','theme.muted','theme.radius','theme.font_body','theme.font_heading',
 'theme.base_size','theme.line_height','theme.card_padding','theme.button_radius','theme.button_height','theme.input_radius'
])->pluck('value','key');
@endphp<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Admin') — {{ $brandName }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"><style>:root{--admin-primary:{{ $theme->get('theme.primary','#55cce7') }};--admin-success:{{ $theme->get('theme.success','#5fd39b') }};--admin-warning:{{ $theme->get('theme.warning','#f2b75e') }};--admin-danger:{{ $theme->get('theme.danger','#ef7777') }};--admin-text:{{ $theme->get('theme.text','#eaf8fb') }};--admin-muted:{{ $theme->get('theme.muted','#86a5b4') }};--admin-radius-lg:{{ (int)$theme->get('theme.radius',18) }}px;--admin-font-body:{{ $theme->get('theme.font_body','Inter, sans-serif') }};--admin-font-heading:{{ $theme->get('theme.font_heading','Inter, sans-serif') }};--admin-base-size:{{ (int)$theme->get('theme.base_size',16) }}px;--admin-line-height:{{ $theme->get('theme.line_height','1.6') }};--admin-card-padding:{{ (int)$theme->get('theme.card_padding',24) }}px;--admin-button-radius:{{ (int)$theme->get('theme.button_radius',10) }}px;--admin-button-height:{{ (int)$theme->get('theme.button_height',42) }}px;--admin-input-radius:{{ (int)$theme->get('theme.input_radius',10) }}px}body{font-family:var(--admin-font-body);font-size:var(--admin-base-size);line-height:var(--admin-line-height)}.admin-title,.admin-nav__group-label,.admin-card__header{font-family:var(--admin-font-heading)}.admin-card__body{padding:var(--admin-card-padding)}.admin-btn{min-height:var(--admin-button-height);border-radius:var(--admin-button-radius)}.admin-input,.admin-select,.admin-textarea{border-radius:var(--admin-input-radius)}</style>
@vite(['resources/css/admin.css'])
@stack('head')
@stack('styles')
</head>
<body>
<div class="admin-shell">
<aside class="admin-sidebar" aria-label="Administration navigation">
  <a class="admin-brand" href="{{ route('admin.dashboard') }}">
    @if($brandLogo)<img src="{{ asset('storage/'.$brandLogo) }}" alt="{{ $brandName }}">@else<span class="admin-brand__mark"><i class="fa-solid fa-bolt"></i></span>@endif
    <span><small>Administration</small><strong>{{ $brandName }}</strong></span>
  </a>
  <nav class="admin-nav">
    @foreach($visibleGroups as $group)
      <div class="admin-nav__group">
        <span class="admin-nav__group-label">{{ $group['label'] }}</span>
        @foreach($group['items'] as $item)
      @php
        $active = request()->routeIs($item['route']) && (!isset($item['params']) || request('type') === ($item['params']['type'] ?? null));
      @endphp
      <a class="{{ $active ? 'is-active' : '' }}" href="{{ route($item['route'], $item['params'] ?? []) }}">
        <span class="admin-nav__icon"><i class="fa-solid {{ $item['icon'] }}"></i></span>
        <span>{{ $item['label'] }}</span>
      </a>
        @endforeach
      </div>
    @endforeach
  </nav>
</aside>

<main class="admin-main">
<header class="admin-topbar">
  <button class="admin-menu-toggle" id="admin-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
  <a class="admin-topbar__brand" href="{{ route('admin.dashboard') }}">
    @if($brandLogo)<img src="{{ asset('storage/'.$brandLogo) }}" alt="{{ $brandName }}">@else<span class="admin-brand__mark"><i class="fa-solid fa-bolt"></i></span>@endif
    <span>{{ $brandName }}</span>
  </a>
  <div class="admin-topbar__actions">
    <a class="admin-profile" href="{{ route('profile') }}" aria-label="My profile"><i class="fa-solid fa-user"></i></a>
  </div>
</header>

<div class="admin-content">
  <div class="admin-container admin-page">
    @yield('content')
  </div>
</div>
</main>
</div>

<div class="admin-mobile-backdrop" id="admin-mobile-backdrop"></div>

<script>
(()=> {
 const body=document.body, toggle=document.getElementById('admin-menu-toggle'), backdrop=document.getElementById('admin-mobile-backdrop');
 if(!toggle || !backdrop) return;
 const close=()=>{body.classList.remove('admin-nav-open');toggle.setAttribute('aria-expanded','false')};
 toggle.addEventListener('click',()=>{const open=!body.classList.contains('admin-nav-open');body.classList.toggle('admin-nav-open',open);toggle.setAttribute('aria-expanded',String(open))});
 backdrop.addEventListener('click',close);
 window.addEventListener('keydown',e=>{if(e.key==='Escape')close()});
})();
</script>
@stack('scripts')
</body>
</html>
