# FuelFree PowerPlant — Website Management Route & Data Map

## Purpose

This map is the implementation checkpoint for the Master Development audit. It records which public website surfaces are controlled by which admin module and identifies the remaining consolidation work before the final CMS UI replacement.

## Source-of-truth rule

Public website content must flow through **Dashboard → Database → Public Website**. Existing dedicated domain models remain authoritative for structured business data. CmsPage is reserved for flexible CMS pages; SiteContentItem owns structured editorial items that are already implemented there.

## Public → Admin mapping

| Public surface | Public route/controller | Current data source | Admin control | Status |
|---|---|---|---|---|
| Home | `/` → HomeController | Homepage settings/sections + approved content modules | Homepage Builder, Website Content, Sliders, Highlights | Implemented |
| Company/About | `/company` → PublicSiteController | SiteContentItem (company) | Website Content → Company & About | Implemented |
| Management | `/management` → ManagementController | SiteContentItem (management) | Website → Management Team | Implemented |
| News | `/news` + `/news/{slug}` → NewsController | SiteContentItem (news) | Website Content → News & Events | Implemented |
| Notices | News controller + announcement records | SiteContentItem (announcement) | Website Content → Notices | Implemented |
| Gallery | `/gallery` → gallery public controller | SiteContentItem (gallery) + GalleryMedia | Website → Gallery | Implemented |
| Resources | `/resources` + `/resources/{slug}` → ResourceController | SiteContentItem (resource/resources) | Website Content → Resources | Implemented in this batch |
| Resource download | `/resources/{slug}/download` | Published resource attachment on SiteContentItem | Website Content → Resources | Implemented in this batch |
| Sustainability | `/sustainability` → SustainabilityController | Structured/site settings content | Existing website content/settings foundation | Audit/polish pending |
| Power Plants | `/power-plants` + `/power-plants/{slug}` | PowerPlant + PlantPerformance | Operations → Power Plants | Implemented foundation |
| Contact | `/contact` GET/POST → ContactController | contact_requests | Communications → Website Inquiries | Implemented |
| Careers | `/career` + application flow | Job/content records + applications | Career Applications / website content | Implemented foundation |
| Flexible CMS pages | `/pages/{slug}` → CmsPageController | CmsPage | Content Pages | Implemented foundation |
| Webmail | `/mail` / `mail.fuelfreepowerplant.com` | cPanel/webmail service | Email | Infrastructure boundary preserved |

## Admin website-management surfaces

| Admin surface | Route family | Primary source | Consolidation decision |
|---|---|---|---|
| Website Content | `/admin/site-content` | SiteContentItem | Unified editorial entry point |
| Navigation | `/admin/navigation` | NavigationMenuItem | Keep separate; controls structure, not content |
| Homepage | `/admin/homepage-builder` | HomepageSection + settings | Keep as composition layer |
| Slider | `/admin/sliders` | SiteSlider | Keep as homepage media module |
| Highlight Banner | `/admin/site-popups` | SitePopup | Keep as global presentation control |
| Management Team | `/admin/management` | SiteContentItem (management) | Keep structured module |
| Gallery | `/admin/galleries` | SiteContentItem + GalleryMedia | Keep dedicated media workflow |
| Resources | `/admin/site-content?type=resource` | SiteContentItem (resource) | Now part of unified Website Content |
| Content Pages | `/admin/cms` | CmsPage | Keep for flexible page-builder content |
| Social Media | `/admin/social-links` | SocialLink | Keep as global identity/settings |
| Design | `/admin/design-builder` | SystemSetting | Keep as presentation controls |
| Theme | `/admin/theme-builder` | SystemSetting | Keep as presentation controls |

## Important architectural boundary

CmsPage and SiteContentItem are intentionally **not merged blindly**. CmsPage handles flexible page-builder documents, while SiteContentItem handles structured editorial records and shared media/attachment workflows. The next consolidation phase should improve the admin information architecture and cross-linking rather than duplicate or migrate production data unnecessarily.

## This batch

Resources were previously publicly renderable but were missing from the unified admin content type list. The batch:

- added `resource` as an admin-managed Website Content type;
- added a Resources entry to the Website navigation;
- added public official-document download handling;
- restricted downloads to published resources with an existing stored attachment;
- added feature coverage for creation, public rendering and download authorization.

## Remaining consolidation work

1. Finish the final Website Content information architecture and cross-links.
2. Complete public-safe Gallery management audit/polish.
3. Audit Sustainability, Careers, Contact and flexible CMS page parity against the canonical blueprint.
4. Add/strengthen end-to-end Dashboard → Database → Public Website integrity tests.
5. Proceed to the next Energy Platform Core batch only after the website-management consolidation checkpoint is stable.

## Infrastructure note

Subdomain provisioning remains outside the Laravel application. `mail.fuelfreepowerplant.com` remains the webmail boundary. No dashboard subdomain CRUD should be introduced.