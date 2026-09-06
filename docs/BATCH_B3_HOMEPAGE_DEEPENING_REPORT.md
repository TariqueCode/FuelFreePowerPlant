# FuelFree PowerPlant — Batch B3 Homepage Deepening Report

Date: 2026-09-07
Branch: `new-design`
PR: #8

## Scope

This batch continues the master roadmap after the public shell work and strengthens the new homepage composition before the wider public-module rebuild.

## Implemented

- Added a production-oriented hero media path using the first active managed slider when available.
- Preserved a safe visual fallback when no hero media is configured.
- Added Open Graph title/description and hero image metadata when media exists.
- Added explicit homepage focus markers for engineering, reliability and future readiness.
- Added richer technology cards with direct navigation to technology, facilities and sustainability areas.
- Standardized the visible product wording to `Projects & Our Plans`.
- Added project/facility media treatment when managed image content exists.
- Added project navigation and a full Projects & Our Plans entry point.
- Added a leadership section driven by the authoritative management content collection.
- Added responsive management profile cards with image fallback and profile links.
- Added a managed gallery section with responsive media grid and accessible alt text.
- Added stronger news item hierarchy and hover/focus treatment.
- Added alternate section surfaces and refined spacing so the page reads as a corporate platform rather than a dashboard.
- Preserved reduced-motion behaviour and keyboard focus treatment.
- Made the production deploy workflow accept an optional `DEPLOY_APP_PATH` secret and default to `/home/fuelfree/Live`, with an explicit directory existence check.
- Kept SQLite as the temporary production database mode while MySQL is unavailable.

## Safety / architecture

- No production merge was performed.
- The new homepage remains isolated behind the staging hostname/design gate.
- No production data deletion was introduced.
- Existing authoritative navigation, management, gallery, news and slider sources remain the data providers.
- No technical performance values were invented or presented as verified/live.

## QA status

GitHub Actions has started a fresh application-quality run for the latest branch changes. The earlier quality run for the preceding homepage commit passed; the newest run was still queued/in progress at report time.

The staging deployment workflow is also configured to run automatically from `new-design`, but the last staging deployment attempt failed before SSH because the required repository secrets (`STAGING_DEPLOY_HOST`, `STAGING_DEPLOY_PORT`, `STAGING_DEPLOY_USER`, `STAGING_DEPLOY_SSH_KEY`, `STAGING_APP_PATH`) are not configured. This is an environment configuration blocker, not an application test failure.

## Release gate

Do not merge PR #8 yet. Required next gates remain:

1. Latest application-quality run green.
2. Staging SSH secrets/path configured and staging deployment successful.
3. Staging smoke test across desktop/mobile navigation, homepage sections, media fallbacks and links.
4. Confirm the actual production application path before release; the workflow now defaults to the previously confirmed `/home/fuelfree/Live` and supports an explicit override secret.
5. Only then prepare the production release/merge.
