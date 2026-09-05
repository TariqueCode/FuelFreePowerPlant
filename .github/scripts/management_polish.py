from pathlib import Path


def replace_once(text: str, old: str, new: str, label: str) -> str:
    count = text.count(old)
    if count != 1:
        raise SystemExit(f"Expected exactly one {label} match, found {count}")
    return text.replace(old, new, 1)


management = Path("resources/views/management/index.blade.php")
footer = Path("resources/views/partials/public-footer.blade.php")

m = management.read_text(encoding="utf-8")
m = replace_once(m, "main{padding:72px 0 88px}", "main{padding:72px 0 32px}", "desktop management main spacing")
m = replace_once(m, "main{padding:55px 0 70px}", "main{padding:55px 0 28px}", "tablet management main spacing")
m = replace_once(m, "main{padding:40px 0 58px}", "main{padding:40px 0 24px}", "mobile management main spacing")
m = replace_once(
    m,
    ".contact i{\n    flex:0 0 26px;",
    ".contact i{\n    flex:0 0 26px;\n    font-family:\"Font Awesome 6 Free\" !important;\n    font-weight:900 !important;",
    "management contact icon font",
)
m = replace_once(
    m,
    ".photo img{width:100%;height:100%;object-fit:cover;display:block}",
    ".photo img{width:100%;height:100%;object-fit:cover;object-position:center center;display:block}",
    "management portrait positioning",
)
management.write_text(m, encoding="utf-8")

f = footer.read_text(encoding="utf-8")
f = replace_once(
    f,
    ".public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.12);padding:46px 0 24px;color:#8aa8b1;font-size:14px;line-height:1.7}",
    ".public-footer{margin-top:60px;border-top:1px solid rgba(86,210,238,.12);padding:46px 0 24px;color:#8aa8b1;font-size:14px;line-height:1.7}\n.public-footer-management{margin-top:28px}",
    "management footer spacing hook",
)
f = replace_once(
    f,
    ".public-footer-contact i{width:16px;color:#51d8f0;margin-top:3px;text-align:center;flex:0 0 16px}",
    ".public-footer-contact i{width:18px;height:18px;display:inline-grid;place-items:center;color:#51d8f0;margin-top:2px;text-align:center;flex:0 0 18px;font-family:\"Font Awesome 6 Free\" !important;font-weight:900 !important;font-style:normal;line-height:1}",
    "footer contact icon rendering",
)
f = replace_once(
    f,
    "<footer class=\"public-footer\">",
    "<footer class=\"public-footer{{ request()->routeIs('management') ? ' public-footer-management' : '' }}\">",
    "management footer class",
)
f = replace_once(
    f,
    "@media(max-width:760px){\n    .public-footer{margin-top:44px;padding:34px 0 20px}",
    "@media(max-width:760px){\n    .public-footer{margin-top:44px;padding:34px 0 20px}\n    .public-footer-management{margin-top:24px;padding:28px 0 16px}",
    "mobile management footer spacing",
)
f = replace_once(
    f,
    ".public-footer-logo{width:38px;height:38px;object-fit:contain;flex:0 0 38px}",
    ".public-footer-logo{width:38px;height:38px;object-fit:contain;object-position:center;display:block;align-self:center;flex:0 0 38px}",
    "footer logo alignment",
)
footer.write_text(f, encoding="utf-8")

print("Management page and footer polish applied successfully.")
