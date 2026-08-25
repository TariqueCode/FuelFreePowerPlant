# FuelFree PowerPlant — Client-First Dynamic Website Blueprint

## Product direction

Build a premium, energy-focused corporate website with a dashboard that gives the client practical control over the complete public website. The reference site `https://deshenergy.com.bd/` is a feature/page reference only; its visual design, colors and branding must not be copied. The production site must not contain a Coming Soon banner/message.

## Non-negotiable architecture

**Dashboard → Database → Public Website** is the primary source-of-truth flow. Every public-editable feature must have an understandable admin control. Avoid duplicate business data structures when an existing model can own the data.

The dashboard must eventually allow the client to create, edit, reorder, publish/unpublish, schedule and remove public website content without developer intervention, including text, images, galleries, videos, links, documents, contact information, branding and announcements.

## Client-facing control center priorities

1. **Website Content** — company profile, about/history, vision, mission, values, chairman/MD message, management/team, announcements, news, sustainability, resources, gallery and other public sections.
2. **News/Notice Editor** — WordPress-style rich editor with headings, lists, quotes, links, multiple image uploads, image galleries, uploaded videos, YouTube embeds, Facebook video embeds and mixed media inside one article/notice.
3. **Homepage Announcement Popup** — admin uploads an image banner, optionally links it to a URL, schedules its active period, and chooses either an automatic close duration (e.g. 3/5/10 seconds or custom seconds) or visitor-controlled close with an X button.
4. **Power Plants/Projects** — complete manual project data entry and public project presentation.
5. **Resources/Documents** — public-safe resources separated from private admin documents.
6. **Gallery** — albums, multiple images, video/media, ordering and publishing.
7. **Contact/Office information** — fully dashboard-controlled.
8. **Branding/Settings** — logo, company name, tagline, favicon and related public identity.
9. **Manual import center** — templates/import for modules where automatic data providers are not available; include preview, validation, duplicate detection and error reporting.

## Reference-feature coverage target

The client requested the useful pages/functions available on the Desh Energy reference website. Therefore the implementation must cover the equivalent corporate information and publishing capabilities, while allowing the client to control them from the dashboard. Feature coverage takes priority over cosmetic work.

Target areas include: Home, Company/About, Vision, Mission, Values, Management, Projects/Power Plants, Project details, News/Notices, Sustainability, Resources/Downloads, Gallery, Contact/Office information, important announcement banners/popups and other public corporate content discovered during the reference-site audit.

## Rich publishing specification

- `SiteContentItem.content` stores trusted rich HTML authored by authorised administrators.
- Editor supports formatting, links, headings, lists and quotes.
- Media upload endpoint stores public article media through Laravel's configured public disk.
- Multiple images can be selected together and inserted into one responsive gallery block.
- Video files can be embedded with native HTML5 controls.
- YouTube and Facebook video embeds are supported through URL-based insertion.
- Public news/notice detail pages render the authored rich content responsively.
- Future-dated and draft content remains private.
- Do not require the client to edit HTML for ordinary publishing tasks.

## Announcement popup specification

- Stored separately in `site_popups` because it is a global presentation control rather than article content.
- Admin can upload a banner image and optionally specify a destination URL.
- Publish state and optional start/end schedule are controlled from admin.
- `display_seconds` null/empty = visitor closes with X; a positive value = automatic close after that many seconds.
- Popup is shown on the homepage only while active.
- Escape key and close button dismiss it immediately.
- Reduced-motion support is required.

## Current implementation status

- [x] Premium dynamic homepage foundation
- [x] Dynamic public CMS page foundation
- [x] Manual Power Plant records and public project route foundation
- [x] Company branding settings foundation
- [x] Mobile hamburger navigation foundation
- [x] Admin Website Content category/filter foundation
- [x] Webmail architecture documented; provisioning remains Admin-only
- [x] News listing/detail foundation
- [x] Sustainability presentation foundation
- [x] Announcement popup database/admin/public rendering foundation
- [x] Rich content editor foundation + media upload endpoint
- [x] Rich multi-image gallery insertion UX
- [x] Dashboard navigation/control-center simplification foundation
- [ ] Finish Company/About public presentation
- [ ] Finish Management/Team public presentation
- [ ] Complete Resources public-safe management
- [ ] Complete Gallery management
- [ ] Complete Contact/Office management
- [ ] Manual import center
- [ ] Reference-site feature parity audit
- [ ] Final global navigation/footer consistency pass
- [ ] End-to-end Dashboard → Database → Public Website integrity verification

## Dashboard navigation rules

- Dashboard Home contains only shortcuts backed by real routes/workflows; do not expose planned-but-unimplemented modules as if they were complete.
- The main desktop navigation is organized around client operations: Website Content, Power Plants, Highlights, Email, Documents, Users, Support and Settings.
- The legacy CMS route is not presented as a primary navigation item because Website Content is the unified public-content control surface.
- Email account creation remains a required admin function and is visible in both desktop navigation and the dashboard control center.
- Subdomain creation is not presented in navigation; cPanel handles subdomains/DNS.
- Technical audit/health tools remain separate from the client-facing website controls unless an authorised role explicitly needs them.

## Responsive dashboard rules

- No page-level horizontal overflow on phone, tablet or desktop.
- Mobile navigation shows a small fixed primary set plus a **More** hamburger drawer; it must not require horizontal swiping.
- Tables may scroll only inside their own bounded table container when their columns genuinely require it.
- Dashboard branding uses the same database-controlled company name and logo as the public website.
- Cards, grids, forms, editors and charts must collapse at appropriate breakpoints rather than forcing a desktop layout onto mobile.

## UX rules

- Mobile-first and responsive across phone, tablet and desktop.
- Font Awesome icons for interface icons.
- Mobile secondary navigation uses the professional hamburger/navigation system.
- Never use `SWIPE LEFT / RIGHT` helper text.
- Premium energy motion should be lightweight and reduced-motion aware.
- The reference site's design is not copied; only useful feature/page requirements are mirrored.

## Data integrity rules

- Never invent plant, financial, environmental or operational statistics.
- Missing approved data must render as unavailable rather than fake zeroes.
- Public content must have a clear admin/database source.
- Manual data remains the source until a verified provider/API is configured.
- Real-data integration remains OFF by default and is controlled from Settings.
- Subdomain creation/management is not a dashboard feature; cPanel handles DNS/subdomains.
- Webmail remains `mail.fuelfreepowerplant.com`; admin creates accounts and users manage their own mail password in webmail.

## Development rule

Before every major implementation step: review this blueprint, update it if requirements changed, inspect the existing implementation, preserve working features, implement the next highest-priority unchecked item, and report the exact commit/change. Feature parity and client control take priority over adding unrelated modules.