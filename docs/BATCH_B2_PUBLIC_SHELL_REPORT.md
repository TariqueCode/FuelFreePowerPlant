# FuelFree PowerPlant — B2 Public Shell Batch

Status: Implemented on `new-design`; production not deployed.

## Scope

B2 establishes the reusable public header/footer boundary for the new website and connects the new homepage to the canonical navigation and social-link sources.

## Implemented

- Reusable new-design header partial.
- Reusable new-design footer partial.
- Canonical public navigation loaded through `PublicNavigationService`.
- Nested navigation dropdown support with keyboard Escape and outside-click close behavior.
- Responsive mobile/tablet navigation drawer.
- Active brand logo from the existing `company.logo_path` setting.
- Active social links from the existing `SocialLink` source.
- New shell stylesheet as a separate Vite entry.
- Reduced-motion handling for new-design reveal behavior.
- New homepage v2 wired behind a staging-only feature flag.

## Production safety

- `main` remains the production branch.
- `FFP_NEW_DESIGN` defaults to `false`.
- New homepage rendering requires both the feature flag and the exact staging host `staging.fuelfreepowerplant.com`.
- The former query-string preview path is not used.
- No production database or production storage mutation is part of this batch.

## Staging deployment

The staging workflow exports `FFP_NEW_DESIGN=true` before rebuilding Laravel's configuration cache. The staging deployment still requires the configured SSH secrets and a real staging application path.

## QA status

Static source review completed for changed application/config/view/navigation files. GitHub Actions staging quality run was triggered from `new-design` and was queued at the time of this record. Browser visual QA is pending a reachable staging domain and deployment.

## Next batch

B3: homepage composition refinement using the supplied brand banner/logo assets, followed by real staging visual QA before any production release decision.
