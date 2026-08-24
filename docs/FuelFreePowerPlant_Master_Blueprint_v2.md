# FuelFreePowerPlant.Com — Master Blueprint v2

> **Canonical product blueprint.** This document supersedes the earlier roadmap where it conflicts with the decisions below. Existing Phase 1–12 implementation is treated as reusable foundation; do not rebuild completed capabilities.

## 1. Product Definition

FuelFreePowerPlant.Com is a premium energy-technology corporate platform with four connected layers:

1. Public Corporate Website
2. Client Portal
3. Admin / Management Control Center
4. Infrastructure / Service Integration

The platform must present the company's technology, solutions, projects, plant/facility information, performance information, sustainability, research, resources and corporate services. The Admin Control Center must control all CMS-managed public content and operational records.

**Important:** “fuel-free” must be technically explained. The platform must never claim unlimited energy or energy creation from nothing without verified engineering evidence.

## 2. Existing Foundation — Reuse, Do Not Rebuild

Phase 1–12 work already completed is the base layer, including:

- Laravel 13 foundation
- Authentication and login throttling
- RBAC and role/permission foundation
- Super Admin / administrator management
- Responsive Admin Dashboard
- Mobile app-style navigation and profile architecture
- Client portal foundation
- Secure private documents
- Folder/file management
- Large-file resumable chunk upload
- Storage usage tracking
- Email-management foundation
- cPanel integration foundation for email services
- CMS foundation
- Security headers and hardening
- Audit/security foundation
- Production deployment foundation

Future work must extend these components rather than duplicate them.

## 3. Design Direction

The visual identity must be original and must **not copy Desh Energy's colours, UI or visual design**.

Use the established FuelFree identity:

- Industrial + futuristic + scientific + trustworthy
- Deep navy/black surfaces
- Electric blue/cyan energy accents
- Controlled sustainability green
- Glass/technical panels
- Energy grids, particles and restrained motion
- SVG/icon-first interface
- Mobile-first responsive implementation
- Professional animated interactions without excessive motion
- English-first release

## 4. Public Website

### Main sitemap

- Home
- About
  - Company Overview
  - Vision
  - Mission
  - Values
  - Leadership
  - Company Journey
- Technology
  - Technology Overview
  - How It Works
  - Architecture
  - Key Components
  - Energy Flow
  - Control & Monitoring
  - Performance
  - Safety
  - Validation & Certifications
  - Technical Documents
  - FAQ
  - Technical Inquiry
- Solutions
- Projects
- Power Plants / Facilities
- Sustainability
- Innovation / R&D
- Resources
- News
- Careers
- Contact
- Client Portal

### Homepage

The homepage should communicate the technology and corporate purpose immediately, with:

- Hero / technology statement
- Technology visualization
- Verified technology metrics
- Energy-flow visualization
- Solutions
- Featured projects
- Power plants/facilities
- Sustainability
- Innovation/R&D
- News
- Final corporate CTA

Live metrics must only be labelled live when a real data source exists.

## 5. Energy / Plant Domain — New Priority

This is the principal missing business layer and becomes the next development focus.

### Plants / Facilities

Each plant/facility may contain:

- Name
- Slug
- Location
- Capacity
- Technology
- Status
- Description
- Commission/start date
- Completion date
- Technical specifications
- Safety information
- Certification/validation records
- Documents
- Performance records
- Public visibility

### Performance

Support future-ready records for:

- Power output
- Energy generated
- Efficiency
- Uptime
- Operating status
- Environmental metrics
- Historical measurements
- Data source
- Measurement timestamp

Every technical value must carry a state such as:

- Verified
- Estimated
- Demonstration
- Target
- Real-time

No unsupported values should be presented as facts.

### Telemetry-ready architecture

The first release does not require physical IoT integration, but the data model and service boundaries must allow future plant telemetry without rewriting the application.

## 6. Projects & Solutions

Admin-controlled project records should support:

- Project name/slug
- Location
- Client/organization
- Capacity
- Technology
- Status
- Overview
- Timeline
- Project documents
- Performance data
- Public/private visibility

Solutions should be configurable CMS content with application/use-case cards rather than hard-coded pages.

## 7. CMS — Admin Controlled Website

The Admin Control Center must be able to manage without code changes:

- Homepage sections
- Static pages
- Page sections
- Navigation
- SEO metadata
- News
- Projects
- Solutions
- Resources
- Careers
- Team
- Gallery/media
- Technology content
- Plant/facility records
- Performance records
- Technical documents
- Contact information
- Footer/social links

Publishing states should include draft/published where appropriate.

## 8. Admin Control Center

### Core navigation

- Dashboard
- Website
  - Pages
  - Sections
  - Navigation
  - SEO
- Energy
  - Plants / Facilities
  - Projects
  - Solutions
  - Performance Data
  - Technology Data
  - Certifications
- Content
  - News
  - Resources
  - Careers
  - Team
  - Gallery
  - Technical Documents
- Communication
  - Contact Requests
  - Messages
  - Newsletter
- Infrastructure
  - Email Accounts
  - Mail Settings
  - Storage
- Users
  - Administrators
  - Staff
  - Clients
  - Roles / Permissions
- System
  - Settings
  - Audit Logs
  - Security
  - Backups

**Subdomain management is intentionally excluded.**

### Dashboard widgets

- Website status
- Active clients
- Active projects
- Plants/facilities
- Current verified power/performance metrics
- Mailboxes
- Storage
- Recent inquiries
- Recent activity
- System/security status

## 9. Client Portal

Client portal navigation:

- Dashboard
- My Projects
- My Documents
- Messages
- Support
- Email Accounts
- Profile
- Security

Do **not** expose subdomain management in the client portal.

## 10. Institutional Email Management

Email services remain an infrastructure feature and may be controlled according to permission:

- Create email account
- Delete email account
- Change password
- Generate secure password
- Change quota
- Suspend / activate
- Forwarders
- Auto responders
- Filters
- Open Webmail
- Account status
- Setup instructions

Supported setup guidance:

- Android
- Windows / Outlook
- Thunderbird
- Linux
- Apple Mail

The application must never expose cPanel credentials or store mailbox passwords in plaintext.

Email creation/control flow:

`Browser → Authorization → Application Service → CpanelService → cPanel UAPI`

## 11. Subdomain Policy — Explicitly Removed

Subdomains are **not an application-managed feature**.

- No Admin subdomain creation
- No Admin subdomain deletion
- No Client subdomain management
- No subdomain CRUD database module
- No subdomain management API
- No subdomain widget in the dashboard

All subdomains will be created and managed directly from cPanel.

The Laravel application may link to known subdomains, but it does not provision or control them.

## 12. Database Direction

Reuse existing tables where already implemented. Extend rather than duplicate.

Core business entities:

- users
- roles
- permissions
- role_user
- permission_role
- clients
- pages
- page_sections
- projects
- project_documents
- documents
- news_posts
- team_members
- job_posts
- contact_requests
- support_tickets
- support_messages
- email_accounts
- audit_logs
- settings

Energy-domain entities to add only if absent:

- plants / facilities
- plant_documents
- performance_records
- technology_records
- certifications / validations
- solutions
- project_solution relations where required

Never store mailbox passwords in plaintext.

## 13. Security

Maintain and extend the existing security foundation:

- HTTPS
- CSRF
- Secure sessions
- Password hashing
- Rate limiting
- Login throttling
- RBAC
- Ownership checks
- File/MIME validation
- Audit logs
- Server-side API credentials
- Environment secrets
- Administrator 2FA
- Security headers
- Safe production errors
- Backup and recovery procedures

## 14. Infrastructure Boundary

Application-managed infrastructure:

- Email account operations through cPanel UAPI
- Webmail links
- Mail setup guidance
- Application storage

cPanel-managed infrastructure:

- Subdomains
- Hosting-level configuration
- Other cPanel resources not explicitly exposed through the application

Credentials remain server-side.

## 15. SEO

Admin-controlled:

- SEO title
- Meta description
- Canonical URL
- Open Graph title/description/image
- Robots directives
- Structured data

Automatic:

- sitemap.xml
- robots.txt
- clean slugs
- breadcrumbs where useful

## 16. Performance

- WebP/AVIF where appropriate
- Lazy loading
- Minimal JavaScript for static content
- Code splitting where useful
- Caching
- Pagination
- Optimized queries
- Compressed assets
- Avoid unnecessary 3D dependencies
- Responsive image containment

## 17. Animation & Responsive Rules

Heavy:

- Hero
- Technology visualization
- Major energy/performance visualizations

Medium:

- Section reveal
- Card hover
- Counters
- Timelines

Light:

- Buttons
- Icons
- Navigation
- Status indicators

Always support `prefers-reduced-motion`.

Test at 320, 360, 390, 414, 480, 768, 820, 1024, 1280, 1440 and 1920+ widths.

## 18. Content Governance

Technical claims must be explicitly classified:

- Verified
- Estimated
- Demonstration
- Target
- Real-time

Public pages must never silently convert estimates or targets into verified facts.

## 19. Development Strategy

### Completed Foundation

Treat Phase 1–12 as existing foundation. Audit and reuse it.

### Next Priority — Energy Platform Core

1. Audit current code against this v2 blueprint.
2. Identify missing energy-domain models/tables.
3. Build Plants/Facilities.
4. Build Solutions.
5. Extend Projects.
6. Build Performance Data architecture.
7. Build Technology/Certification records.
8. Connect these to Admin Control Center.
9. Expose approved data to public pages.
10. Add future telemetry-ready service boundaries.

### After Energy Core

11. Complete CMS-controlled Public Website.
12. Complete Admin content management.
13. Complete Client Portal business workflows.
14. Complete institutional Email/cPanel integration.
15. Security and QA.
16. Production deployment.

## 20. Definition of Done

A feature is complete only when:

- It is controlled by the correct role/permission.
- Admin can manage it without editing source code where CMS control is intended.
- Public/client visibility follows authorization and publishing rules.
- Mobile and desktop UI are responsive.
- Audit/security requirements are satisfied.
- No duplicate functionality is introduced.
- Technical claims have an explicit evidence/status classification.

## 21. Final Architecture

```text
                    FuelFreePowerPlant.Com
                              │
          ┌───────────────────┼───────────────────┐
          │                   │                   │
       Public              Client              Admin
       Website             Portal             Control Center
          │                   │                   │
          └───────────────────┼───────────────────┘
                              │
                       Laravel Application
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
      MySQL                Storage              cPanel UAPI
        │                     │                     │
   Energy/CMS/Users     Private Documents      Email Services
        │                                           │
        └────────────── Plant/Project Data ─────────┘
                              │
                    Future Plant Telemetry
```

## 22. Strategic Rule

**Do not spend the next development cycles polishing secondary infrastructure while the core Energy/Power Plant product is incomplete.** Existing file management, upload, profile, authentication and infrastructure foundations should be maintained and hardened only when necessary. The primary development effort must now move toward the energy-technology, corporate, project, plant, performance and CMS capabilities that define FuelFreePowerPlant.Com.
