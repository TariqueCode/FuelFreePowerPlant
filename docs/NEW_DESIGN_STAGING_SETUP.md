# New Design Staging Setup

## Confirmed production boundary

The current cPanel production domain `live.fuelfreepowerplant.com` uses document root `/home/fuelfree/Live` according to the supplied cPanel screenshot.

The existing GitHub production workflow currently deploys `main` to `/home/fuelfree/public_html`. These are separate facts and must not be silently reconciled by assumption.

## Staging target

The intended preview domain is:

`https://staging.fuelfreepowerplant.com/`

The exact cPanel document root and application path for staging are intentionally **not hard-coded** until provisioned and verified. The staging workflow reads `STAGING_APP_PATH` from a GitHub Actions secret.

## Required GitHub Actions secrets

- `STAGING_DEPLOY_HOST`
- `STAGING_DEPLOY_PORT`
- `STAGING_DEPLOY_USER`
- `STAGING_DEPLOY_SSH_KEY`
- `STAGING_APP_PATH`

No production credentials belong in this workflow.

## Required staging environment

The staging application must use a staging `.env` with:

- a separate database from production;
- separate application/storage credentials where appropriate;
- non-production mail delivery or a safe mail sink;
- `APP_ENV=staging`;
- `APP_DEBUG=false`;
- staging URL configuration.

Do not copy the production `.env` into staging.

## Deployment flow

`new-design` push → quality checks → frontend build → tests → staging SSH deployment → staging cache rebuild → browser visual QA.

The workflow never deploys to `main` or the production path.

## Current status

- `new-design` branch: created
- manual/automatic staging workflow: created
- staging domain: not provisioned/verified yet
- staging document root: not provisioned/verified yet
- staging database: not provisioned/verified yet
- staging GitHub secrets: not verified yet
