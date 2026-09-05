# FuelFreePowerPlant

FuelFreePowerPlant is a Laravel 13-based corporate energy platform and secure operations portal. The platform combines the public corporate website, admin control center, projects, power-plant/facility information, performance data, secure documents, institutional email and support workflows.

> **Important:** The application must never present invented, simulated, estimated or target values as live/verified plant data. A value may be labelled **real-time/verified** only after it comes from an approved source and passes the project's validation rules.

## Current platform foundation

- Laravel 13 application structure
- Authentication with login throttling
- Role and permission based access control
- Responsive dark operations dashboard
- Private per-user document storage
- Folder creation, rename, move, copy and deletion
- File upload with resumable 512 KB chunking
- Secure file download and ownership checks
- Storage quota tracking
- Client portal foundation
- Permission-aware Email and Support modules
- Power Plant and Plant Performance domain models
- Dashboard KPI/graph foundation for plant statistics and verified performance

## Development

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

For production, configure the database, mail transport, storage, cache/queue and web-server limits in `.env` before deployment.

## Real Energy Data Integration

The dashboard is designed to consume **real plant data**, but the current application does not automatically know the physical plant's measurements. Real data integration requires a trusted data source and an approved ingestion path.

### 1. Decide where the real data comes from

Before writing an integration, document the actual source for each metric:

| Metric | Example source | Required decision |
|---|---|---|
| Power output (kW) | Plant PLC/SCADA, inverter, power meter or approved API | Identify the exact device/API and unit |
| Energy generated (kWh) | Revenue/energy meter, SCADA or approved API | Identify cumulative vs interval value |
| Efficiency (%) | Approved plant calculation or source system | Document the calculation/formula |
| Uptime (%) | SCADA/monitoring system | Define the uptime calculation period |
| Environmental metrics | Approved sensors/system | Document sensor, unit and sampling interval |

Do **not** connect an unknown public API, random web page, or manually invented value and label it as live plant data.

### 2. Prepare the physical/data source

The engineering/operations team must provide the actual source details before integration:

- Plant/facility name and unique identifier
- Device/meter/SCADA system name
- Manufacturer/model where applicable
- Communication method (REST API, MQTT, Modbus gateway, database, CSV/SFTP, etc.)
- Endpoint or gateway address
- Authentication method
- Available metrics/tags
- Units and scaling factors
- Sampling frequency
- Timestamp/timezone rules
- Whether the value is instantaneous, interval-based or cumulative
- Source reliability and expected failure behaviour

Credentials, API keys and passwords must **never** be committed to GitHub. Store secrets in `.env`/server secret storage.

### 3. Create a server-side ingestion layer

Real plant data should enter Laravel through a controlled backend integration—not directly from browser JavaScript to a plant device.

Recommended flow:

```text
Plant / Meter / SCADA
        |
        v
Approved Gateway / API / Connector
        |
        v
Laravel ingestion service
        |
        +--> validation + unit checks
        |
        +--> source/timestamp/status metadata
        |
        v
plant_performance
        |
        +--> Admin Dashboard
        +--> Project/Plant pages
        +--> Public metrics (only when approved)
```

Create a dedicated service, for example:

```text
app/Services/Energy/PlantDataIngestionService.php
```

The service should normalize incoming measurements into the application's units and persist them through the `PowerPlant` / `PlantPerformance` models.

### 4. Map the source to a Power Plant

Every incoming record must be associated with an existing `power_plants.id`.

Do not create a new plant automatically from an unknown data source. An administrator must first create/approve the plant and its source mapping.

At minimum, the mapping should identify:

```text
source_system
source_plant_id
power_plant_id
```

If multiple meters exist, keep their source identifiers separately so the origin of every measurement can be traced.

### 5. Validate every measurement before it becomes trusted data

The ingestion layer should validate:

- Correct plant/source mapping
- Required timestamp
- Timestamp timezone
- Numeric values
- Expected units
- Plausible ranges
- Duplicate records
- Out-of-order records
- Missing measurements
- Communication/source errors
- Meter reset/rollover for cumulative kWh values

Rejected data should not silently become verified data. Store an error/status record for investigation where appropriate.

### 6. Use explicit data status

`plant_performance.data_status` is part of the data-governance model.

Use statuses according to the actual provenance, for example:

- `real-time` — currently arriving from an approved live source
- `verified` — checked/approved historical measurement
- `estimated` — explicitly calculated/estimated and labelled as such
- `demonstration` — test/demo data only
- `target` — planned target, not measured output

**Only `real-time` or `verified` records may be used for dashboard elements labelled as live/verified.** Estimated, demonstration and target values must never be mixed into those figures.

### 7. Store the source and audit information

Each performance record should retain enough metadata to answer:

> “Where did this number come from, when was it received, and can we trust it?”

At minimum use:

- `source`
- `measured_at`
- `data_status`
- plant/source identifier
- ingestion timestamp (add a dedicated field if required by the integration)
- validation result/error information where required

For regulated or business-critical measurements, add an immutable ingestion/audit trail rather than overwriting the original value.

### 8. Do not make the dashboard poll the plant directly

The browser should read application data from Laravel. It should not contain plant credentials or directly connect to PLC/SCADA devices.

For near-real-time requirements, use a backend polling/queue worker or an approved push gateway. The dashboard can then refresh through an authenticated endpoint, polling, SSE or WebSocket layer as appropriate.

### 9. Handle stale/offline data correctly

A live-looking number must not remain labelled `LIVE` forever when the plant source stops sending data.

Define a source-specific freshness threshold. If the last accepted measurement becomes stale:

- mark the source/reading stale or offline
- show the last measurement time
- remove the `LIVE` label
- show a clear status such as `Data source offline` or `Last verified reading`
- alert authorized operators when appropriate

The threshold must be chosen from the actual plant/source requirements rather than invented globally.

### 10. Testing before production

Use a staging/test source first. Never connect an untested connector directly to production.

Test at least:

1. Normal measurement ingestion
2. Invalid values
3. Missing fields
4. Duplicate timestamps/records
5. Out-of-order timestamps
6. Meter reset/rollover
7. Source timeout
8. Authentication failure
9. Network failure
10. Stale data
11. Unit conversion
12. Multiple plants
13. Permission-restricted dashboard access
14. Recovery after the source reconnects

Only after these tests pass should the source be marked approved for production.

### 11. Production deployment checklist

Before enabling real data in production:

- Confirm the plant/source owner
- Confirm written metric definitions and units
- Confirm source credentials and secret storage
- Confirm firewall/network rules
- Confirm server timezone and plant timezone handling
- Confirm queue/scheduler/worker configuration
- Confirm monitoring and failure alerts
- Confirm database backups
- Confirm audit logging
- Confirm stale-data behaviour
- Confirm dashboard labels
- Confirm who is authorized to approve a source as verified

### 12. What the developer needs from the plant/engineering team

To implement the **actual connector**, provide these details for each plant:

```text
Plant name:
Plant ID / source ID:
Location / timezone:
Data source type:
Vendor / system:
API or gateway URL (if applicable):
Authentication method:
Available metric names/tags:
Power output unit:
Energy generated unit:
Efficiency source/formula:
Uptime source/formula:
Environmental metrics + units:
Sampling interval:
Cumulative or interval energy:
Expected freshness/timeout:
Test/staging endpoint:
Production endpoint:
```

**Do not send passwords, API secrets or private keys in chat or commit them to GitHub.** Those should be configured directly in the server's environment/secret store.

## Data model

The current energy foundation contains:

- `power_plants` — approved plant/facility definitions
- `plant_performance` — timestamped performance measurements linked to a plant

The performance model currently supports power output, energy generated, efficiency, uptime, environmental metrics, source, notes and explicit data status.

The dashboard consumes these records for its plant statistics and performance indicators. If no approved real data exists, the UI should show an empty/awaiting state rather than manufacture values.

## Admin control

The intended production workflow is:

```text
Admin creates/approves Plant
        ↓
Admin configures/approves Data Source
        ↓
Connector receives measurements
        ↓
Validation
        ↓
Plant Performance records
        ↓
Dashboard analytics
        ↓
Approved public/project metrics
```

Only authorized administrators should be able to approve a data source or change data-governance status.

## Subdomains

Subdomain creation and management are intentionally **not part of this Laravel application**. Subdomains are created and managed directly from cPanel. The application must not add a dashboard-based subdomain creation workflow.

## Security

Private documents are stored outside the public web root and are served through authenticated controller actions. Upload sessions are bound to the authenticated user and chunk offsets are validated before data is written.

Energy data integrations must follow the same security principle: plant credentials stay server-side, source access is permission-controlled, incoming data is validated, and sensitive source details are never exposed to public pages.

## Project structure

- `app/Http/Controllers` — application controllers
- `app/Models` — Eloquent models
- `app/Services` — business/integration services
- `database/migrations` — database schema
- `database/seeders` — roles and permissions
- `resources/views` — Blade UI
- `routes/web.php` — authenticated web routes

## Responsive CMS editor

The CMS rich-text editor is designed to remain usable on compact screens without removing its editing capabilities. On mobile, the editor toolbar stays on a single horizontal touch-scrollable rail, while the editor canvas remains width-constrained to the device. CMS media insertion supports selecting multiple image/video files in one action, and inserted images can be resized from their selection handle while retaining responsive max-width behavior.

## License

This project is application-specific software. Review the repository's deployment and licensing requirements before redistribution.
