# Geumcheon Asset Management API

Laravel backend that powers the Asset Management mobile client. It exposes REST endpoints for authentication, dashboard metrics, asset CRUD (+ photo uploads), user assignment data, and request workflows.

## Features

- Sanctum token-based authentication (/api/auth/login, /api/auth/logout, /api/me).
- Dashboard summary endpoint with category and status breakdowns.
- Asset catalogue with filtering, search, and pagination.
- Asset creation/update supporting metadata, custodian assignments, and photo uploads.
- User directory scoped by department and is_active flag.
- Asset requests and assignments (controllers/resources already scaffolded).

## Requirements

- PHP 8.2+
- Composer 2+
- MySQL 8+ (or compatible)
- Node.js 18+ (optional, for front-end assets)

## Setup

`ash
cp .env.example .env
composer install
php artisan key:generate
`

Configure .env:

`ini
APP_URL=http://192.168.100.62:8000
SANCTUM_STATEFUL_DOMAINS=192.168.100.62:8000
SESSION_DOMAIN=192.168.100.62
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geumcheon_assets
DB_USERNAME=root
DB_PASSWORD=secret
`

### Database

`ash
php artisan migrate --seed
`

Seeds include demo roles, categories, assets, and users (with passwords hashed). Adjust seeders as needed for staging/production.

### Storage & Media

`ash
php artisan storage:link
`

Asset photos uploaded from the Flutter client are saved in storage/app/public/asset-photos. The API returns both sset_photo_path and a public sset_photo_url so the mobile app can display images.

### Running the API

`ash
php artisan serve --host=0.0.0.0 --port=8000
`

Or configure your preferred server (Valet, Sail, nginx, Apache). Ensure the host/port matches the Flutter client's ApiConfig.baseUrl.

## API Overview

| Method | Endpoint | Description |
| --- | --- | --- |
| POST | /api/auth/login | Obtain Sanctum token |
| POST | /api/auth/logout | Revoke current token |
| GET | /api/me | Current user profile |
| GET | /api/dashboard | Totals, critical counts, categories |
| GET | /api/assets | List assets (filters: sset_category_id, status, search) |
| POST | /api/assets | Create asset (multipart with optional sset_photo) |
| PUT | /api/assets/{id} | Update asset (supports sset_photo or emove_asset_photo=1) |
| DELETE | /api/assets/{id} | Delete asset |
| GET | /api/users | Fetch assignable users (filtered by department) |
| Resource | /api/asset-requests | CRUD for asset requests |
| Resource | /api/asset-assignments | Manage assignments |

All endpoints require Authorization: Bearer <token> except login.

## Uploading Asset Photos

- Use multipart/form-data with field name sset_photo.
- To remove an existing photo, send emove_asset_photo=1 (without uploading a new file).
- Laravel stores files on the public disk; configure filesystem drivers if using S3/MinIO.

## Tests

Run the feature tests (extend as needed):

`ash
php artisan test
`

## Deployment Notes

- Configure web server to serve /public as the document root.
- Ensure APP_URL matches your accessible domain so the delivered photo URLs are correct.
- Queue workers can be enabled later for heavy background jobs; current flows run synchronously.

## License

Internal project for Geumcheon. Redistribution requires permission from the maintainers.
