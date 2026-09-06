# FuelFree PowerPlant — New Website Rebuild Audit

Date: 2026-09-06
Baseline branch: `main`
Baseline production commit: `78315ea036842e95678479e5060732a80284553f`
Development branch: `new-design`
Safety snapshot: `backup/pre-new-website-rebuild-2026-09-06`

## Audit rule

This document is the baseline for a completely new website. The existing application is treated as a reference, content/data source and capability inventory. Its visual implementation is **not** a design constraint.

No new-design code is permitted on `main` until final approval and release QA.

## 1. Repository / runtime audit

### Confirmed

- Repository: `TariqueCode/FuelFreePowerPlant`
- Default branch: `main`
- Laravel 13 / PHP 8.4 application
- Vite + Tailwind CSS 4 frontend build
- Blade-based public site and admin UI
- GitHub Actions quality workflow
- GitHub Actions production deployment workflow
- cPanel deployment target under `/home/fuelfree/public_html`
- Production deploy is triggered by pushes to `main`
- Production deployment runs quality checks first, then SSH deployment
- Deployment performs Composer install, frontend build, migrations, storage link and Laravel cache rebuild
- Existing project documentation already records security/data-governance and architecture decisions

### Important current-state finding

There is **no staging deployment workflow or staging environment configuration visible in the repository audit**. The new rebuild therefore requires a separate staging deployment path before the first visual milestone is considered complete.

The requested `staging.fuelfreepowerplant.com` hostname and its cPanel document root/database are not verifiable from GitHub alone and must be provisioned in cPanel without touching production.

The live URL could not be runtime-fetched by the available browser/network environment during this audit. Therefore production runtime health is **not marked verified** from this audit.

## 2. Branch / release safety

Existing repository history contains numerous backup/checkpoint branches. For this rebuild two explicit safety branches were created from the current `main`:

- `backup/pre-new-website-rebuild-2026-09-06`
- `new-design`

`main` remains the production branch.

Release target:

```text
main
  = current production

new-design
  = complete new website development

release/new-design-v1
  = final approved release snapshot
```

A production release must have a recoverable previous stable commit/branch before any merge to `main`.

## 3. Current application architecture

### Backend

- Laravel 13
- Controllers under `app/Http/Controllers`
- Admin controllers under `app/Http/Controllers/Admin`
- Eloquent models under `app/Models`
- Business/integration services under `app/Services`
- Shared support utilities under `app/Support`
- Database migrations and seeders under `database/`

### Frontend

- Blade views under `resources/views`
- Public layout: `resources/views/layouts/public.blade.php`
- Admin layouts/views under `resources/views/admin`
- Public CSS currently concentrated in `resources/css/app.css`
- `resources/js/app.js` is currently minimal
- Vite is the asset build system
- Existing views include multiple homepage variants and large Blade files; these are legacy implementation candidates, not a design source for the rebuild

## 4. Existing public functionality inventory

Confirmed route/domain inventory includes:

- Home
- About Us / company pages
- Plants / facilities-related content
- Future projects
- Solutions
- Gallery and gallery detail
- Management and management contact card
- News and news detail
- Sustainability
- Career and chunked application upload
- Contact and inquiries
- CMS pages
- Public/shared document download
- Favicon
- Webmail entry and webmail application
- Authenticated dashboard/profile/client portal foundations

## 5. Existing admin capability inventory

Confirmed from routes, controllers, views, models and repository tree:

- Dashboard
- Users
- Roles and permissions
- Website content
- CMS / Page Builder foundation
- Homepage Builder
- Navigation/Menu Builder
- Management/Profile administration
- Gallery
- Sliders
- Site popups/highlights
- Social Links
- Help Desk
- Inquiries
- Career applications
- Mail / institutional email foundation
- Documents / file management
- Audit log
- System health
- Settings
- Design / theme builder foundations

## 6. Feature classification

### KEEP

- Authentication and login throttling
- RBAC and permission middleware
- Audit logging
- System health controls
- Private document storage and ownership checks
- Resumable chunk uploads
- Storage quota/usage foundation
- Help Desk and inquiry workflows
- Career applications
- Institutional webmail/mail integration
- Gallery/media records
- Management profiles and contact card capability
- Social media links
- News/content records
- CMS/page publishing foundation
- Navigation source-of-truth concepts
- Plant/performance data governance
- Existing technical-data status model (`real-time`, `verified`, `estimated`, `demonstration`, `target`)
- SEO fields already present in CMS foundations

### REUSE CONCEPT

- Homepage as a composition layer
- CMS structured blocks
- Global header/framework/footer flags
- Navigation source registry and public navigation service
- Profile ordering/publication concepts
- Central media/file source
- Publishing permission separation
- Server-side energy data ingestion concept
- Existing security-header and ownership-check patterns

### REBUILD

- Entire public website UI and information architecture
- Global header/footer/navigation visual system
- Public component library
- Homepage composition
- Public page templates
- Admin shell and navigation
- Page Builder UX and data model boundaries
- Profile Builder UX
- Menu Builder UX
- File Manager UX
- Social Media Manager UX
- Settings organization
- Design system / theme system
- Staging deployment pipeline
- Release/rollback workflow
- Visual QA and responsive system

### REMOVE / RETIRE FROM NEW UI

These are **candidates**, not yet destructive deletions:

- Legacy homepage variants such as `home-v2.blade.php` and `home-v3.blade.php` if no runtime dependency remains
- Legacy `welcome.blade.php` if confirmed unused
- Redundant legacy CMS/resource pathways already superseded by the current canonical navigation/page-builder architecture
- Legacy UI-only wrappers that duplicate current navigation/content sources

No data or production records are to be removed merely because a legacy UI is retired.

## 7. Database audit

The migration history is extensive and shows several reconciliation/repair migrations around navigation, CMS/page-builder, gallery, help desk and content structures. This is a strong signal that the new website must avoid reproducing schema duplication or repair-on-repair patterns.

Important existing domains include:

- users
- roles / permissions
- documents / folders
- audit logs
- CMS pages/settings
- site content
- site popups
- gallery media
- management profiles/folders
- social links
- sliders
- career applications
- inquiries
- help desk replies/emails
- navigation menu items
- homepage sections
- power plants
- plant performance
- email accounts

### Migration rule for new website

Do not rewrite the existing production schema blindly. First produce a field-level mapping:

```text
Existing table/field
        ↓
New canonical entity/field
        ↓
Migration / transform rule
        ↓
Verification query
```

The new design should initially read the existing authoritative data where practical, then migrate only when a new canonical data model is proven necessary.

## 8. Security audit baseline

Confirmed foundations include:

- password hashing
- role/permission checks
- login throttling
- CSRF protection through Laravel request flow
- private document storage outside public web root
- authenticated download actions
- ownership checks
- security headers middleware
- server-side cPanel/integration boundaries
- environment-based secrets
- audit logging

### Gaps / rebuild requirements

- Exactly-one-Super-Admin invariant is not obviously enforced at the role/data-model level. The current creation command can promote an account to Super Admin and does not itself enforce uniqueness.
- Page Builder custom-code mode is not currently a canonical first-class architecture. The current CMS model supports structured blocks/content and global flags, but no `custom_code` field was found in the inspected model/search baseline.
- New custom-code support must allow HTML/CSS/JavaScript only, sanitize/validate where applicable, isolate dangerous capabilities, and never execute PHP/shell/server-side code.
- File upload security must remain deny-by-default for executable server-side formats.

## 9. Testing / CI audit

Current CI has useful foundations:

- PHP syntax linting
- Composer installation
- SQLite migration test environment
- package discovery
- platform requirement check
- Vite build
- unit tests
- feature tests

Current feature tests cover areas including:

- capability matrix
- content pages
- homepage builder
- homepage settings
- navigation integrity
- portal capability navigation
- publishing authority
- site content rendering
- system settings scope
- user role delegation
- security headers
- management profile views
- legacy power-plant removal

### CI limitations for new rebuild

- No browser/E2E visual regression suite is visible in the current repository baseline.
- No dedicated staging smoke test is visible.
- No responsive viewport automation is visible.
- No performance-budget gate is visible.
- No accessibility automated gate is visible.

These become release requirements for the new website.

## 10. Deployment audit

Current production deployment is main-only. The workflow explicitly resets the production checkout to `origin/main`, installs dependencies, runs migrations, builds frontend assets, links storage and rebuilds Laravel caches.

This is appropriate for a stable production branch but **must not be reused unchanged for `new-design` staging**, because staging needs its own:

- hostname
- document root
- environment file/secrets
- database
- storage location
- cache/session/queue configuration
- deployment concurrency
- rollback path

## 11. Source / brand audit

### Official supplied logo

The supplied logo is a circular metallic emblem with:

- dominant green core
- electric blue directional arrows
- silver/chrome rings
- green embossed typography
- lightning/energy symbol at the center
- strong industrial/engineering character

### Official supplied banner

The supplied banner establishes a much more explicit premium technology direction:

- deep navy/black left field
- electric blue/cyan energy glow
- white typography
- metallic silver engineering surfaces
- blue luminous flow lines
- futuristic generator/turbine visual
- restrained green presence from the brand identity

The banner therefore becomes the primary visual-direction source for the rebuild, while the logo remains the identity anchor.

### Brand direction derived from supplied assets

Primary surface: deep navy / near-black

Primary accent: electric blue / cyan

Secondary brand accent: controlled green

Neutral system: cool silver / white / slate

Visual effects: restrained blue glow, metallic edge highlights, fine technical grid/line motifs

Typography mood: geometric, technical, clean and corporate; avoid decorative/fantasy fonts

Component mood: layered dark surfaces, fine borders, soft depth, controlled glass/blur only where it improves hierarchy

## 12. New information architecture — baseline

Public:

1. Home
2. About
3. Technology
4. Solutions
5. Projects & Our Plans
6. Power Plants / Facilities
7. Sustainability
8. Innovation / R&D
9. Resources / Corporate Documents
10. News & Events
11. Gallery
12. Careers
13. Contact
14. Client Portal

The exact labels may be simplified after content migration mapping, but the architecture must remain simple and canonical.

Admin:

- Overview
- Website
  - Home
  - Pages
  - Navigation
  - SEO
- Content
  - News & Events
  - Notices
  - Gallery
  - Management
  - Corporate Documents
  - Careers
- Business / Energy
  - Projects & Our Plans
  - Solutions
  - Plants / Facilities
  - Performance
  - Technology / Validation
- Communication
  - Help Desk
  - Inquiries
  - Email
  - Social Media
- Assets
  - File Manager
- System
  - Settings
  - Users / Roles
  - Audit Log
  - System Health

## 13. New frontend architecture

Use a component-first Blade architecture with a single tokenized design system.

Recommended layers:

```text
resources/views/new/
  layouts/
  components/
    brand/
    navigation/
    hero/
    sections/
    cards/
    media/
    forms/
    data/
    feedback/
  pages/
  partials/

resources/css/new/
  tokens.css
  base.css
  components.css
  utilities.css
  motion.css

resources/js/new/
  app.js
  navigation.js
  motion.js
  media.js
  accessibility.js
```

No duplicated desktop/mobile markup unless there is a measured semantic reason.

## 14. New admin architecture

Admin should be organized by user intent rather than by historical controller/view names.

Use a stable shell:

```text
AdminShell
 ├─ Sidebar / command navigation
 ├─ Topbar
 ├─ Breadcrumbs
 ├─ Page header / actions
 ├─ Content region
 └─ Feedback / modal layer
```

Module pages should share table, form, filter, modal, empty-state and permission-aware action components.

## 15. Page Builder architecture

Three modes:

- CMS — structured blocks and governed fields
- Custom Code — HTML/CSS/JS only, explicitly permission-gated and sanitized/contained
- Hybrid — structured CMS plus controlled custom code

Each page stores explicit:

- status
- draft/published timestamps
- revision metadata
- author/editor metadata
- SEO metadata
- global header flag
- global framework flag
- global footer flag
- builder representation
- optional safe custom-code representation

Publishing must remain permission-gated.

## 16. File Manager architecture

Central asset source with:

- folder tree
- breadcrumbs
- search
- list/grid modes
- upload/chunk upload
- rename
- move/copy
- delete
- bulk actions
- download
- direct-link copy
- details
- image dimensions
- MIME/type display
- permission checks

No arbitrary executable upload/execution.

## 17. Social media architecture

One canonical `social_links` source with:

- platform/name
- icon identifier
- URL
- active state
- ordering
- optional custom platform

Consumed by header/footer/contact and other approved surfaces.

## 18. Responsive / accessibility baseline

Target widths:

`320, 360, 390, 414, 480, 768, 820, 1024, 1280, 1440, 1920+`

Required:

- keyboard navigation
- visible focus
- semantic landmarks
- form labels/errors
- alt text
- heading hierarchy
- sufficient contrast
- reduced motion
- touch-friendly controls

## 19. Performance baseline

- WebP/AVIF where appropriate
- responsive images
- lazy loading below the fold
- minimal JS for static pages
- transform/opacity-based motion
- no unnecessary heavy 3D framework
- cacheable public data
- pagination for admin datasets
- optimized database queries
- controlled third-party scripts
- performance budgets added to CI later

## 20. Staging architecture

Target:

```text
main
  ↓ production
live.fuelfreepowerplant.com

new-design
  ↓ staging deployment
staging.fuelfreepowerplant.com
```

Staging must use a separate database from production. If file assets are copied, they must be copied deliberately and with access controls; production private documents must not become publicly exposed through staging.

Required cPanel setup before first staging deployment:

1. Create staging subdomain in cPanel.
2. Create separate staging document root.
3. Create separate staging database/user.
4. Configure staging environment secrets.
5. Configure separate storage path.
6. Configure staging mail transport to a safe/test destination.
7. Configure staging scheduler/queue only if required.
8. Add GitHub Actions staging secrets.
9. Add staging deploy workflow triggered by `new-design`.
10. Add staging smoke test after deployment.

## 21. Release / rollback architecture

```text
backup/pre-new-website-rebuild-2026-09-06
          ↓
new-design
          ↓
final QA
          ↓
release/new-design-v1
          ↓
approved merge to main
          ↓
production
```

Rollback must restore the previous stable application release and preserve database compatibility. Database rollback is not assumed to be automatic; migration compatibility must be planned before release.

## 22. Development roadmap

### Batch 0 — Audit / Blueprint
- Complete current-state audit
- Preserve production safety snapshot
- Create `new-design`
- Finalize design system and IA
- Define staging requirements

### Batch 1 — New Design Foundation
- New token system
- typography
- container/grid
- global navigation
- footer
- reusable component primitives
- motion/accessibility foundation

### Batch 2 — Homepage
- hero
- company narrative
- technology visualization
- projects/solutions
- management
- news/gallery
- sustainability
- CTA/footer

### Batch 3 — Public Content Architecture
- About
- Technology
- Solutions
- Projects & Our Plans
- Plants/Facilities
- Sustainability
- Resources
- News
- Gallery
- Careers
- Contact

### Batch 4 — Admin Rebuild
- admin shell
- overview
- page/content management
- profile builder
- menu builder
- social media
- file manager
- settings

### Batch 5 — Page Builder
- CMS
- Custom Code
- Hybrid
- global flags
- revisions
- preview/publish/rollback

### Batch 6 — Data Migration / Integration
- field mapping
- content migration
- media mapping
- profile mapping
- navigation mapping
- verification

### Batch 7 — Staging / QA
- staging deploy
- browser QA
- responsive QA
- accessibility QA
- security QA
- performance QA
- SEO QA

### Batch 8 — Release
- release snapshot
- production approval
- main merge
- production smoke test
- rollback verification

## 23. Definition of Done

A batch is complete only when:

- implementation exists on `new-design`
- automated tests pass
- build passes
- responsive behavior is tested
- security impact is reviewed
- accessibility impact is reviewed
- production data is untouched unless explicitly approved
- staging is verified where applicable
- commit SHA is recorded
- known issues are disclosed

Anything not actually tested is reported as **not verified**.
