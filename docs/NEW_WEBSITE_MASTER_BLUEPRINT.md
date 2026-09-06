# FuelFree PowerPlant — NEW WEBSITE MASTER BLUEPRINT

Status: Architecture baseline only — no production design implementation
Branch: `new-design`
Baseline: `main@78315ea036842e95678479e5060732a80284553f`

## 1. Product principle

Build a genuinely new FuelFree PowerPlant digital platform rather than patching the legacy UI.

```text
Understand → Audit → Extract → Design → Architect → Rebuild → Test → QA → Stage → Release
```

The existing system remains the operational reference and production safety net.

## 2. Product layers

```text
FuelFree PowerPlant
│
├── Public Corporate Website
├── Admin / Management Control Center
├── Client Portal
└── Infrastructure Integrations
     ├── Database
     ├── Private/Public Storage
     ├── Mail / cPanel UAPI
     └── Future Plant Data Sources
```

## 3. Brand language

### Primary visual direction

Premium dark corporate energy technology.

### Source-derived identity

The supplied logo combines green energy branding, electric-blue directional flow, chrome/silver engineering surfaces and a central lightning symbol.

The supplied banner adds deep navy/black, bright cyan-blue energy flow, white typography, metallic machinery and a futuristic industrial environment.

### Design tokens — initial direction

```text
--color-bg-0        : near-black navy
--color-bg-1        : deep navy surface
--color-bg-2        : elevated navy/slate surface
--color-brand-green : controlled FuelFree green
--color-energy      : electric blue
--color-cyan        : luminous cyan
--color-silver      : cool metallic neutral
--color-text        : high-contrast white
--color-text-muted  : cool slate
--color-border      : low-alpha silver/blue
```

Exact hex values will be sampled/validated from the supplied brand assets during the visual-design batch rather than guessed from generic templates.

### Typography

Use a clean geometric/technical sans-serif family with:

- strong display weights
- compact navigation labels
- highly readable body copy
- numeric/tabular-friendly treatment for metrics
- consistent tracking

Avoid decorative or generic “gaming” typography.

## 4. Visual system

### Surfaces

Use layered surfaces instead of a flat black canvas:

1. page background
2. section surface
3. elevated card
4. interactive surface
5. modal/overlay

### Borders

Fine, low-contrast borders with occasional energy-blue focus/active treatment.

### Glow

Glow is reserved for:

- primary CTA
- technology/energy visualization
- active states
- important metrics
- hero focal elements

### Cards

Cards should communicate engineering precision:

- clear hierarchy
- restrained radius
- controlled depth
- consistent internal spacing
- strong hover/focus state

## 5. Motion system

Motion must support meaning, not decoration.

```text
Heavy:
  hero energy visualization
  technology visualizations
  major performance graphs

Medium:
  section reveal
  project cards
  counters
  timeline

Light:
  navigation
  buttons
  icons
  status changes
```

All animation uses transform/opacity where possible and must respect `prefers-reduced-motion`.

## 6. Public information architecture

### Home

Hero → company value → technology → solutions → projects → facilities/performance → sustainability → innovation → news/gallery → management/trust → CTA → footer.

### About

- Company Overview
- Vision
- Mission
- Values
- Leadership
- Journey
- Corporate Documents

### Technology

- Overview
- How It Works
- Architecture
- Key Components
- Energy Flow
- Control & Monitoring
- Performance
- Safety
- Validation / Certifications
- Technical Documents
- FAQ
- Technical Inquiry

### Solutions

Configurable use-case/solution records rather than hard-coded marketing pages.

### Projects & Our Plans

A unified project/plans experience with:

- current projects
- future plans
- project status
- location
- capacity
- technology
- timeline
- documentation

### Plants / Facilities

Technical facility records with explicit data provenance.

### Sustainability

Environmental and sustainability narrative plus verified metrics where available.

### Innovation / R&D

Technology development, research, engineering and future-readiness content.

### Resources

Corporate documents, technical documents, downloads and approved public resources.

### News & Events

One canonical content source; homepage uses the same source rather than duplicate records.

### Gallery

Albums → media → captions → featured media.

### Careers

Open roles and application workflow.

### Contact

Corporate contact, inquiry form, social channels and help/support entry points.

## 7. Global header

One reusable header component.

Desktop:

- logo
- primary navigation
- active state
- optional CTA
- social/utility access where justified

Mobile/tablet:

- same navigation data source
- single responsive component strategy
- keyboard and focus-safe menu
- accessible drawer/dialog semantics

No duplicated desktop/mobile content sources.

## 8. Global footer

Reusable footer containing:

- logo
- concise corporate description
- navigation groups
- contact
- social media
- documents/resources
- careers
- legal
- copyright

## 9. Admin information architecture

```text
Overview

Website
  Home
  Pages
  Navigation
  SEO

Content
  News & Events
  Notice
  Gallery
  Management
  Corporate Documents
  Careers

Business / Energy
  Projects & Our Plans
  Solutions
  Plants / Facilities
  Performance
  Technology / Validation

Communication
  Help Desk
  Inquiries
  Email
  Social Media

Assets
  File Manager

System
  Settings
  Users / Roles
  Audit Log
  System Health
```

## 10. Admin UX architecture

Every module follows the same interaction model:

```text
Module overview
  ↓
Filter/search
  ↓
List/grid
  ↓
Create/Edit
  ↓
Preview
  ↓
Save draft
  ↓
Publish/approve (permission gated)
  ↓
Audit trail
```

Shared UI primitives:

- DataTable
- FilterBar
- Search
- Pagination
- EmptyState
- StatusBadge
- PermissionAction
- FormField
- RichTextEditor
- MediaPicker
- ConfirmDialog
- Toast/Alert
- Drawer/Modal
- RevisionPanel

## 11. Page Builder architecture

### Data model

```text
Page
├── identity
├── slug
├── status
├── content source
├── template
├── builder blocks
├── custom HTML
├── custom CSS
├── custom JS
├── global header flag
├── global framework flag
├── global footer flag
├── SEO
└── revision metadata
```

### Modes

**CMS**

Structured fields and approved reusable blocks.

**Custom Code**

HTML/CSS/JavaScript only. No PHP, shell, arbitrary server execution or unrestricted filesystem access.

**Hybrid**

Structured CMS content plus controlled custom code.

### Publishing lifecycle

```text
Draft → Preview → Review → Approved → Published
                       ↓
                    Unpublish
                       ↓
                    Restore
```

Revision history is mandatory for production-grade page editing.

## 12. Profile Builder

Authoritative management profile source.

Fields:

- photo
- name
- designation
- biography
- contact
- visiting card
- slug
- ordering
- publication state
- SEO metadata

Unique slug must be enforced at database/application level.

## 13. Menu Builder

Canonical navigation source.

Supports:

- menus
- parent/child hierarchy
- ordering
- drag/drop
- internal page link
- external URL
- custom URL
- active/inactive
- label override

The public header and footer consume this source; they do not maintain parallel navigation definitions.

## 14. Social Media module

Canonical record:

```text
platform
name
icon
url
active
order
```

Supported defaults:

- Facebook
- YouTube
- LinkedIn
- X/Twitter
- Instagram
- WhatsApp
- Custom

## 15. File Manager

### UI

- folder tree
- breadcrumbs
- search
- grid/list
- drag/drop where safe
- multi-select
- bulk action toolbar
- details drawer

### Operations

- create folder
- rename
- move
- copy
- delete
- upload
- chunked upload
- download
- copy direct link

### Security

- authorization on every operation
- canonicalized paths
- path traversal protection
- MIME/content validation
- executable upload restrictions
- no arbitrary server-side execution
- private files served through authorized controllers

## 16. Energy / plant data governance

Every technical metric must retain provenance.

```text
Source → Ingestion → Validation → Stored measurement → Approved presentation
```

Allowed states:

- real-time
- verified
- estimated
- demonstration
- target

Only real-time/verified records may receive live/verified labels.

The website must never manufacture technical performance values.

## 17. SEO architecture

Per-page controls:

- SEO title
- meta description
- canonical
- Open Graph
- social image
- robots directives
- structured data where applicable
- alt text
- clean slug

Automatic:

- sitemap
- robots
- canonical fallback
- breadcrumbs where useful

## 18. Accessibility architecture

Accessibility is built into component primitives:

- semantic HTML
- landmark regions
- labels
- descriptions
- keyboard interactions
- focus management
- visible focus
- contrast tokens
- reduced motion
- screen-reader status messaging
- alt text rules

## 19. Performance architecture

### Images

- responsive `srcset`
- WebP/AVIF where useful
- explicit dimensions
- lazy load below fold
- priority loading for hero/LCP image

### JavaScript

- minimal baseline bundle
- page/module enhancement only when needed
- no unnecessary SPA conversion
- transform/opacity animation

### Backend

- eager loading where required
- pagination
- query indexes
- caching of public navigation/settings
- cache invalidation after publish

## 20. Security architecture

Layers:

```text
Browser
 ↓
CSRF / session protection
 ↓
Authentication
 ↓
Authorization / permission
 ↓
Validation
 ↓
Business service
 ↓
Persistence
 ↓
Audit
```

Required protections:

- XSS
- SQL injection through framework-bound queries/validation
- CSRF
- IDOR
- mass assignment
- upload abuse
- path traversal
- privilege escalation
- session abuse
- rate abuse
- unsafe custom code

## 21. Super Admin model

Exactly one active Super Admin is required by product policy.

The rebuild must enforce this through a service/database invariant rather than relying only on UI behaviour.

Required controls:

- current-password verification
- strong password policy
- secure password change
- session invalidation where appropriate
- login activity
- audit trail
- future 2FA readiness

## 22. Data migration strategy

```text
Analyze
  ↓
Map
  ↓
Transform
  ↓
Stage migration
  ↓
Verify counts/content/relationships
  ↓
Publish
```

No production data deletion as part of a visual rebuild.

Media migration must preserve references or provide a deterministic remapping table.

## 23. Staging architecture

```text
GitHub
│
├── main ─────────→ Production
│                    live.fuelfreepowerplant.com
│
└── new-design ───→ Staging
                     staging.fuelfreepowerplant.com
```

Staging environment:

- separate database
- separate storage
- separate secrets
- safe mail transport
- no production destructive jobs
- deployment from `new-design`
- smoke test after deploy

## 24. Release architecture

```text
new-design
   ↓
QA green
   ↓
release/new-design-v1
   ↓
production approval
   ↓
main
   ↓
production smoke test
```

Rollback:

```text
New release
   ↓
Previous stable release
   ↓
Production
```

Never rewrite production history to perform a rollback.

## 25. Batch roadmap

### B0 — Audit & Blueprint
Completed as documentation baseline.

### B1 — Design System
Tokens, typography, grid, components, motion, accessibility primitives.

### B2 — Public Shell
Header, footer, navigation, base layout, SEO shell.

### B3 — Homepage
New composition with supplied banner/logo visual direction.

### B4 — Public Modules
About, technology, solutions, projects, plants, sustainability, resources, news, gallery, careers, contact.

### B5 — Admin Shell
New admin navigation and reusable module UI.

### B6 — Management Tools
Page Builder, Profile Builder, Menu Builder, Social Media, File Manager.

### B7 — Data Migration
Content/media/profile/navigation migration and verification.

### B8 — Staging
Separate environment, deployment, smoke tests.

### B9 — Full QA
Functional, responsive, accessibility, security, performance, SEO, browser.

### B10 — Release
Release snapshot, production approval, deploy, runtime smoke test, rollback verification.

## 26. Master acceptance criteria

The new website is release-ready only when:

- visually distinct from the legacy website
- brand-driven from supplied assets
- dark-first and premium
- responsive at required breakpoints
- keyboard accessible
- reduced-motion compliant
- performant
- SEO-ready
- secure
- CMS-manageable
- permission-aware
- data-governed
- staging verified
- production rollback path verified
- no unverified claim presented as verified/live
