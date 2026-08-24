# FuelFreePowerPlant

FuelFreePowerPlant is a Laravel 13-based secure client and operations portal for managing users, private documents, email services and support workflows.

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

## Security

Private documents are stored outside the public web root and are served through authenticated controller actions. Upload sessions are bound to the authenticated user and chunk offsets are validated before data is written.

## Project structure

- `app/Http/Controllers` — application controllers
- `app/Models` — Eloquent models
- `database/migrations` — database schema
- `database/seeders` — roles and permissions
- `resources/views` — Blade UI
- `routes/web.php` — authenticated web routes

## License

This project is application-specific software. Review the repository's deployment and licensing requirements before redistribution.
