# FuelFree PowerPlant — Master Development Audit Baseline

## Batch 1 — Admin/CMS Architecture Baseline

Baseline branch: main
Baseline commit: a1732d211cd253d3740ab330d64d6f2760585f85
Purpose: Preserve findings that guide implementation batches.

## Confirmed Admin domains
- Dashboard
- Users / roles / permissions
- Power plants / plant performance
- Navigation
- Website content
- Site popups / highlights
- Sliders
- Management
- Galleries
- Help desk / mail / inquiries
- Career applications
- Documents
- Homepage builder
- Design / theme builder
- CMS pages
- Settings
- Social links
- Audit / health

## Architectural decisions
1. Retain existing role/permission middleware as the security foundation.
2. Simplify website management into a small set of human-readable modules.
3. Keep structured content in dedicated modules; use the CMS editor for flexible pages.
4. Treat Homepage as a composition layer consuming existing content modules.
5. Preserve highlights/site-popup functionality during CMS simplification.
6. Evolve navigation toward folder/page hierarchy without duplicating content records.
7. Put editorial/static charts and graphs inside the CMS editor; keep operational plant charts data-driven.
8. Gate technical controls such as raw HTML by permission.
9. Use safe deletion/recovery patterns where practical.
10. Never remove production data merely to simplify the UI.

## CMS editor upgrade backlog
Table: row/column insert/delete; resizing; merge/split; alignment; practical width/height; responsive behavior.
Images: visual resize; aspect-ratio lock; crop; replace; alignment; caption; alt text; media-library selection.
Media/content blocks: improved video; button; chart; bar chart; line chart; pie/doughnut chart; responsive preview.
Reliability: autosave/draft recovery; version history/restore; preview; permission-aware editing; HTML sanitization.

## Multi-admin target model
Permission model: Role -> Module -> Action -> Workflow
Core actions: View / Create / Edit / Delete / Publish / Schedule / Approve
Controlled publishing: Draft -> Review -> Approved -> Published

## Production safety
- Do not create admin.fuelfreepowerplant.com until deployment architecture is ready.
- Preserve mail.fuelfreepowerplant.com webmail service.
- Request cPanel Terminal actions explicitly when required.
- Database migrations and destructive changes require impact and rollback checks.

## Batch status
Completed: architecture baseline recorded.
Not yet implemented: CMS/navigation redesign and editor enhancements.
Next major batch: consolidate the existing website-management surface and map each current route/controller/data source into the final Global CMS model before UI replacement.