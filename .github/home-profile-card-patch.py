from pathlib import Path

path = Path('resources/views/home-v3.blade.php')
text = path.read_text()
marker = '</style>\n<main class="shell home-v3">'
if 'HOMEPAGE MANAGEMENT PROFILE CARD V4' in text:
    raise SystemExit('already patched')
css = r'''<style>
/* === HOMEPAGE MANAGEMENT PROFILE CARD V4 ===
   Final responsive composition approved for the homepage.
   Desktop: two horizontal cards, 4:5 portrait + right-side profile data.
   Tablet/mobile: two cards per row, square 1:1 portrait. */
.home-v3 .home-section-management .management-grid{width:100%!important;display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:clamp(12px,1.8vw,20px)!important;align-items:stretch!important}
.home-v3 .home-section-management .member-card{min-width:0!important;width:100%!important;max-width:none!important;height:auto!important;display:grid!important;overflow:hidden!important;border-radius:20px!important;grid-template-columns:minmax(120px,42%) minmax(0,58%)!important;grid-template-rows:1fr!important;align-items:stretch!important}
.home-v3 .home-section-management .member-photo{grid-column:1!important;grid-row:1!important;width:100%!important;height:auto!important;aspect-ratio:4/5!important;align-self:stretch!important;overflow:hidden!important}
.home-v3 .home-section-management .member-photo img{width:100%!important;height:100%!important;object-fit:cover!important}
.home-v3 .home-section-management .member-body{grid-column:2!important;grid-row:1!important;min-width:0!important;min-height:0!important;height:100%!important;display:flex!important;flex-direction:column!important;justify-content:flex-start!important;padding:clamp(14px,1.5vw,20px)!important}
.home-v3 .home-section-management .member-body h3{margin:0!important;font-size:clamp(15px,1.35vw,20px)!important;line-height:1.25!important;text-align:left!important;overflow-wrap:anywhere!important}
.home-v3 .home-section-management .member-role{margin:5px 0 0!important;font-size:clamp(8px,.75vw,10px)!important;line-height:1.4!important;text-align:left!important}
.home-v3 .home-section-management .member-contacts{display:grid!important;gap:5px!important;margin:11px 0 0!important;padding:0!important;border:0!important}
.home-v3 .home-section-management .member-contact{display:flex!important;min-width:0!important;align-items:flex-start!important;gap:7px!important;font-size:clamp(8px,.72vw,10px)!important;line-height:1.4!important;white-space:normal!important}
.home-v3 .home-section-management .member-contact span{min-width:0!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.home-v3 .home-section-management .member-message{display:-webkit-box!important;min-height:0!important;height:auto!important;max-height:4.55em!important;margin:12px 0 0!important;padding:0!important;border:0!important;font-size:clamp(9px,.78vw,11px)!important;line-height:1.52!important;-webkit-line-clamp:3!important;line-clamp:3!important;overflow:hidden!important;overflow-wrap:anywhere!important}
.home-v3 .home-section-management .member-more{flex:0 0 auto!important;width:auto!important;align-self:flex-start!important;min-height:36px!important;margin:13px 0 0!important;padding:8px 12px!important;border-radius:10px!important;justify-content:center!important;gap:8px!important;font-size:9px!important;white-space:nowrap!important}
@media(max-width:650px){
.home-v3 .home-section-management .management-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:9px!important}
.home-v3 .home-section-management .member-card{display:flex!important;flex-direction:column!important;grid-template-columns:none!important;grid-template-rows:none!important;border-radius:14px!important}
.home-v3 .home-section-management .member-photo{width:100%!important;height:auto!important;aspect-ratio:1/1!important;flex:0 0 auto!important}
.home-v3 .home-section-management .member-body{width:100%!important;height:auto!important;display:flex!important;flex:0 0 auto!important;padding:10px!important}
.home-v3 .home-section-management .member-body h3{font-size:clamp(10px,3.1vw,13px)!important;text-align:center!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.home-v3 .home-section-management .member-role{font-size:clamp(7px,2vw,9px)!important;text-align:center!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}
.home-v3 .home-section-management .member-contacts{display:grid!important;gap:4px!important;margin-top:9px!important}
.home-v3 .home-section-management .member-contact{font-size:clamp(7px,1.9vw,9px)!important;text-align:left!important}
.home-v3 .home-section-management .member-contact span{white-space:nowrap!important}
.home-v3 .home-section-management .member-message{margin-top:9px!important;font-size:clamp(7.5px,2vw,9px)!important;line-height:1.5!important;-webkit-line-clamp:3!important;line-clamp:3!important}
.home-v3 .home-section-management .member-more{width:100%!important;min-height:34px!important;margin-top:9px!important;align-self:stretch!important;padding:7px!important;font-size:clamp(7px,1.9vw,9px)!important}
}
@media(min-width:651px) and (max-width:1099px){
.home-v3 .home-section-management .member-card{grid-template-columns:minmax(110px,38%) minmax(0,62%)!important}
.home-v3 .home-section-management .member-body{padding:13px!important}
}
@media(max-width:420px){
.home-v3 .home-section-management .management-grid{gap:8px!important}
.home-v3 .home-section-management .member-card{border-radius:12px!important}
.home-v3 .home-section-management .member-body{padding:8px!important}
.home-v3 .home-section-management .member-contacts{margin-top:7px!important;gap:3px!important}
.home-v3 .home-section-management .member-message{margin-top:7px!important}
.home-v3 .home-section-management .member-more{margin-top:7px!important;min-height:32px!important}
}
</style>
<main class="shell home-v3">'''
if marker not in text:
    raise SystemExit('marker not found')
path.write_text(text.replace(marker, css, 1))
