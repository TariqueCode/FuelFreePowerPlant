<?php

namespace App\Http\Middleware;

use App\Models\HomepageSection;
use App\Models\SitePopup;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAnnouncementPopup
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->routeIs('home') && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && HomepageSection::query()->where('key', 'highlight')->value('is_enabled') !== false) {
            $popup = SitePopup::active()->first();
            if ($popup && $response->getContent()) {
                $seconds = $popup->display_seconds ? (int) $popup->display_seconds : 0;
                $delay = $seconds > 0 ? "setTimeout(closePopup, {$seconds}000);" : '';
                $href = $popup->link_url ? htmlspecialchars($popup->link_url, ENT_QUOTES, 'UTF-8') : '';
                $image = asset('storage/'.$popup->image_path);
                $title = htmlspecialchars($popup->title ?: 'Important announcement', ENT_QUOTES, 'UTF-8');
                $target = $href ? "<a class=\"announcement-link\" href=\"{$href}\" target=\"_blank\" rel=\"noopener\">" : '';
                $targetEnd = $href ? '</a>' : '';
                $html = <<<HTML
<div id="site-announcement" class="site-announcement" role="dialog" aria-modal="true" aria-label="{$title}"><div class="announcement-backdrop"></div><div class="announcement-card">{$target}<img src="{$image}" alt="{$title}">{$targetEnd}<button type="button" class="announcement-close" onclick="closePopup()" aria-label="Close announcement"><i class="fa-solid fa-xmark"></i></button></div></div><style>.site-announcement{position:fixed;inset:0;z-index:9999;display:grid;place-items:center;padding:18px}.announcement-backdrop{position:absolute;inset:0;background:rgba(0,8,13,.78);backdrop-filter:blur(8px);animation:popupFade .25s ease}.announcement-card{position:relative;z-index:1;width:min(920px,94vw);max-height:90vh;border:1px solid rgba(100,220,244,.28);border-radius:22px;overflow:hidden;background:#061a25;box-shadow:0 30px 100px rgba(0,0,0,.6),0 0 55px rgba(40,190,225,.1);animation:popupIn .35s cubic-bezier(.2,.8,.2,1)}.announcement-card img{display:block;width:100%;max-height:82vh;object-fit:contain;background:#031018}.announcement-link{display:block}.announcement-close{position:absolute;right:12px;top:12px;width:42px;height:42px;border:1px solid rgba(255,255,255,.22);border-radius:50%;background:rgba(2,12,18,.72);color:#fff;display:grid;place-items:center;cursor:pointer;backdrop-filter:blur(10px)}.announcement-close:hover{background:rgba(67,194,229,.2);border-color:rgba(120,225,245,.5)}@keyframes popupFade{from{opacity:0}to{opacity:1}}@keyframes popupIn{from{opacity:0;transform:translateY(18px) scale(.97)}to{opacity:1;transform:none}}@media(prefers-reduced-motion:reduce){.announcement-backdrop,.announcement-card{animation:none}}</style><script>function closePopup(){const p=document.getElementById('site-announcement');if(p)p.remove()}document.addEventListener('keydown',e=>{if(e.key==='Escape')closePopup()});{$delay}</script>
HTML;
                $response->setContent(str_replace('</body>', $html.'</body>', $response->getContent()));
            }
        }
        return $response;
    }
}
