<?php
$siteName = $brand['name'];
$sectionTitle = $titles[$section] ?? ucfirst(str_replace('-', ' ', $section));
$intro = $section === 'gallery' ? 'Events, activities, milestones and selected moments from Fuel Free Power Plant.' : ($page?->excerpt ?: $brand['tagline']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($sectionTitle) ?> — <?= e($siteName) ?></title>
<style>
:root{--bg:#030c12;--text:#effcff;--muted:#91aeb8;--line:rgba(93,211,238,.15);--cyan:#4fd2ee}*{box-sizing:border-box}body{margin:0;background:linear-gradient(180deg,#020a10,#061721 55%,#020a10);color:var(--text);font-family:Inter,system-ui,sans-serif}a{text-decoration:none;color:inherit}.shell{width:min(1180px,calc(100% - 28px));margin:auto}.header{position:sticky;top:0;z-index:20;background:rgba(2,10,16,.94);border-bottom:1px solid var(--line)}.nav{height:68px;display:flex;align-items:center;justify-content:space-between}.brand{font-weight:800}.menu{display:flex;gap:4px}.menu a{font-size:10px;padding:9px;color:#9bb6bf}.menu-btn{display:none}.hero{padding:60px 0 30px}.eyebrow{font-size:9px;letter-spacing:.2em;color:var(--cyan);text-transform:uppercase}.hero h1{font-size:clamp(38px,6vw,70px);margin:10px 0}.hero p{max-width:760px;color:var(--muted);line-height:1.8}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.card,.rich,.empty{border:1px solid var(--line);border-radius:18px;background:rgba(7,30,42,.72);overflow:hidden}.card img{width:100%;height:185px;object-fit:cover;display:block}.body,.rich{padding:20px}.body h2,.rich h2{margin:0 0 8px}.body p,.meta,.rich{color:var(--muted);font-size:11px;line-height:1.8}.pill{display:inline-block;margin-top:10px;padding:5px 8px;border-radius:20px;background:rgba(79,210,238,.08);color:#80d8e9;font-size:8px}.empty{padding:35px;text-align:center;color:var(--muted)}.footer{margin-top:60px;padding:30px 0;color:#688893;font-size:9px;border-top:1px solid var(--line)}.gallery-cover{height:210px!important}.gallery-date{color:#7ed9e9;font-size:9px;margin-bottom:8px}@media(max-width:760px){.menu-btn{display:block;background:none;border:1px solid var(--line);color:#fff;padding:10px}.menu{display:none;position:absolute;top:68px;left:12px;right:12px;flex-direction:column;background:#06131b;padding:10px}.menu.open{display:flex}.grid{grid-template-columns:1fr 1fr}}@media(max-width:520px){.grid{grid-template-columns:1fr}.hero{padding-top:45px}.hero h1{font-size:43px}}</style>
</head>
<body>
<header class="header"><div class="shell nav">
<a class="brand" href="<?= route('home') ?>"><?= e($siteName) ?></a>
<button class="menu-btn" type="button">☰</button>
<nav class="menu">
<a href="<?= route('home') ?>">Home</a><a href="<?= route('site.about') ?>">About Us</a><a href="<?= route('site.solutions') ?>">Solutions</a><a href="<?= route('site.plants') ?>">Plants</a><a href="<?= route('site.future-project') ?>">Future Project</a><a href="<?= route('site.gallery') ?>">Gallery</a><a href="<?= route('management') ?>">Management</a><a href="<?= route('site.career') ?>">Career</a><a href="<?= route('contact') ?>">Contact</a>
</nav></div></header>
<main class="shell">
<section class="hero"><span class="eyebrow"><?= e($siteName) ?></span><h1><?= e($sectionTitle) ?></h1><p><?= e($intro) ?></p></section>
<?php if ($page?->content): ?><section class="rich"><?= $page->content ?></section><?php endif; ?>
<?php if ($section === 'about-us'): ?>
<?php if ($companyItems->isEmpty()): ?><div class="empty">No company information has been published yet.</div><?php else: foreach ($companyItems as $item): ?><article class="rich"><?php if ($item->image_path): ?><img src="<?= asset('storage/'.$item->image_path) ?>" alt="<?= e($item->title) ?>" style="max-width:100%;border-radius:12px"><?php endif; ?><h2><?= e($item->title) ?></h2><?php if ($item->excerpt): ?><p><?= e($item->excerpt) ?></p><?php endif; ?><?= $item->content ?></article><?php endforeach; endif; ?>
<?php elseif ($section === 'solutions'): ?>
<section class="grid"><?php if ($items->isEmpty()): ?><div class="empty">No solutions have been published yet.</div><?php else: foreach ($items as $item): ?><article class="card"><?php if ($item->image_path): ?><img src="<?= asset('storage/'.$item->image_path) ?>" alt="<?= e($item->title) ?>"><?php endif; ?><div class="body"><h2><?= e($item->title) ?></h2><?php if ($item->excerpt): ?><p><?= e($item->excerpt) ?></p><?php endif; ?><?= $item->content ?></div></article><?php endforeach; endif; ?></section>
<?php elseif ($section === 'gallery'): ?>
<section class="grid"><?php if ($items->isEmpty()): ?><div class="empty">No gallery moments have been published yet.</div><?php else: foreach ($items as $item): ?><article class="card"><?php if ($item->image_path): ?><img class="gallery-cover" src="<?= asset('storage/'.$item->image_path) ?>" alt="<?= e($item->title) ?>" loading="lazy"><?php endif; ?><div class="body"><div class="gallery-date"><?= $item->published_at?->format('d F Y') ?></div><h2><?= e($item->title) ?></h2><?php if ($item->excerpt): ?><p><?= e($item->excerpt) ?></p><?php endif; ?><?php if ($item->content): ?><div><?= $item->content ?></div><?php endif; ?></div></article><?php endforeach; endif; ?></section>
<?php elseif ($section === 'plants' || $section === 'future-project'): ?>
<section class="grid"><?php if ($projects->isEmpty()): ?><div class="empty">No projects have been published yet.</div><?php else: foreach ($projects as $project): ?><a class="card" href="<?= route('projects.show', $project->slug) ?>"><div class="body"><h2><?= e($project->name) ?></h2><div class="meta"><?= e($project->location ?: 'Location not specified') ?><br><?= $project->capacity_kw !== null ? number_format((float)$project->capacity_kw,1).' kW' : 'Capacity not specified' ?> · <?= e($project->technology ?: 'Technology not specified') ?></div><span class="pill"><?= e($project->status) ?></span></div></a><?php endforeach; endif; ?></section>
<?php if ($items->isNotEmpty()): ?><section class="grid" style="margin-top:18px"><?php foreach ($items as $item): ?><article class="card"><?php if ($item->image_path): ?><img src="<?= asset('storage/'.$item->image_path) ?>" alt="<?= e($item->title) ?>"><?php endif; ?><div class="body"><h2><?= e($item->title) ?></h2><p><?= e($item->excerpt) ?></p><?= $item->content ?></div></article><?php endforeach; ?></section><?php endif; ?>
<?php elseif ($section === 'career'): ?>
<section class="grid"><?php if ($items->isEmpty()): ?><div class="empty">No career information has been published yet.</div><?php else: foreach ($items as $item): ?><article class="card"><?php if ($item->image_path): ?><img src="<?= asset('storage/'.$item->image_path) ?>" alt="<?= e($item->title) ?>"><?php endif; ?><div class="body"><h2><?= e($item->title) ?></h2><p><?= e($item->excerpt) ?></p><?= $item->content ?></div></article><?php endforeach; endif; ?></section>
<?php endif; ?>
</main>
<footer class="footer"><div class="shell">© <?= date('Y') ?> <?= e($siteName) ?> · All rights reserved.</div></footer>
<script>const b=document.querySelector('.menu-btn'),m=document.querySelector('.menu');b?.addEventListener('click',()=>m.classList.toggle('open'));</script>
</body></html>
