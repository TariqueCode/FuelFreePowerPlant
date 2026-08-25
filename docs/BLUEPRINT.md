# FuelFree PowerPlant — Dynamic Website Blueprint

## Product direction

Build a premium, energy-focused corporate website whose public content is controlled from the Laravel administration system. The Coming Soon banner is not part of the production website and must not be reintroduced.

## Current architecture

- Public homepage: database-driven company branding, CMS content, statistics and power-plant records.
- CMS pages: published pages are loaded dynamically.
- Power Plants: manually entered by authorised administrators; public project data comes from the same records.
- Admin Website Content: structured company, management, news, sustainability, gallery, resource and announcement content.
- Company branding: name, domain, tagline and logo are managed through Settings.
- Mail: administrator creates mail accounts; mailbox access is a separate webmail entry point at `mail.fuelfreepowerplant.com`. Login is intended to use mail address + mail password; users may change their own mail password after login. No public mailbox provisioning or self-registration is required. Subdomain/DNS is configured in cPanel.
- No dashboard feature for creating/managing subdomains is required.

## Public website roadmap

1. Dynamic premium homepage — foundation complete.
2. Public Power Plant detail pages — route/design foundation added at `/projects/{slug}`.
3. Company/About page — CMS content foundation exists; next: premium public presentation and navigation integration.
4. Management/Team — CMS content foundation exists; next: structured professional presentation.
5. News & Updates — **NEXT: build listing/detail presentation driven by published content.**
6. Sustainability — structured content with energy/environment metrics where approved data exists.
7. Resources/Documents — public-safe resources only; private admin documents remain protected.
8. Gallery — dynamic media presentation.
9. Contact/Inquiry — controlled contact information and enquiry workflow.
10. Global navigation/footer — dynamic CMS links, mobile hamburger navigation and webmail access; continue consistency pass across all public pages.
11. Dashboard ↔ public-site integrity — every public statistic/content block must have a clear admin/database source.
12. Real-data integration — remain disabled until a real provider/API is configured and verified; settings must control activation.

## Current implementation order

- [x] Premium dynamic homepage foundation
- [x] Dynamic public CMS page foundation
- [x] Manual Power Plant records and public project route foundation
- [x] Company branding settings foundation
- [x] Mobile hamburger navigation foundation
- [x] Admin Website Content category/filter foundation
- [x] Webmail architecture decision documented; provisioning remains Admin-only
- [ ] Finish Company/About public presentation
- [ ] Finish Management/Team public presentation
- [ ] Build News & Updates listing + detail views
- [ ] Build Sustainability presentation
- [ ] Build Resources public-safe listing/detail views
- [ ] Build Gallery
- [ ] Build Contact/Inquiry
- [ ] Final global navigation/footer consistency pass
- [ ] End-to-end dashboard/public data integrity verification

## UX rules

- Mobile-first and responsive across phone, tablet and desktop.
- Use Font Awesome icons rather than fragile custom icon glyphs.
- Mobile secondary navigation lives inside the professional hamburger menu.
- Do not use `SWIPE LEFT / RIGHT` helper text.
- Energy motion should be premium and lightweight: CSS-based ambient motion, responsive intensity and reduced-motion support.
- Customer-provided banner is a visual reference only; do not copy its Coming Soon messaging into the production website.

## Data integrity rules

- Do not invent plant statistics.
- Manual plant data is valid only after administrator entry.
- Public pages should gracefully show unavailable fields instead of fake values.
- Do not duplicate the same business data into separate public-only tables when an existing model already owns it.
- Before each feature, verify its source of truth in the database/model and keep Dashboard → Database → Public Website as the primary data flow.

## Development rule

Before each major implementation step, review and update this blueprint, then implement the next unchecked item. If a new requirement changes architecture, record it here before proceeding.