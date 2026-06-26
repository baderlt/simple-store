# Wany Bio Namecheap Shared Hosting Deployment Guide

This guide documents safe production settings for deploying the Laravel store on Namecheap shared hosting. Redis is not required for this deployment; use database-backed cache, sessions, and queues.

## Required server stack

- PHP 8.2 or newer
- PHP extensions: `bcmath`, `ctype`, `curl`, `dom`, `exif`, `fileinfo`, `filter`, `gd`, `intl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`
- MySQL or MariaDB with `utf8mb4`
- Composer 2
- Node.js only during build, not required at runtime if assets are built before deployment
- Web root pointed to the Laravel `public` directory

## Production `.env` baseline

```dotenv
APP_NAME="Wany Bio"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wanybio.com

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

FILESYSTEM_DISK=public

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Run `php artisan key:generate --force` once on first deployment if `APP_KEY` is empty. Never commit a real production `.env` file.

## Database

Use MySQL or MariaDB in production:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_strong_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

Before deploying, back up the database. Then run:

```bash
php artisan migrate --force
```

## Namecheap shared hosting profile

Use database drivers because Redis and process supervisors are normally not available on shared hosting:

```dotenv
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

Set a cron job to process queued work:

```bash
* * * * * cd /home/USER/path-to-app && php artisan queue:work database --stop-when-empty --tries=3 --timeout=90 >> /dev/null 2>&1
```

This keeps checkout email/notification work from depending on long-running daemons.

Do not set `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, or `QUEUE_CONNECTION=redis` on this hosting plan.

## Build and deploy commands

Run these during deployment:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

If the server cannot run Node.js, build assets locally or in CI and upload the generated `public/build` directory.

## Writable directories

The web server user must be able to write:

- `storage`
- `bootstrap/cache`

Uploaded images are served through:

```bash
php artisan storage:link
```

## Health check

Laravel is configured with the built-in health endpoint:

```text
/up
```

Use it for uptime monitors or load balancer checks. It should return HTTP 200 when the application is bootable.

## Cache clearing after admin changes

The application already clears storefront/settings caches when admin-managed content changes. After deployments, run:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Security checklist

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` uses HTTPS
- `SESSION_SECURE_COOKIE=true`
- Admin accounts use strong passwords
- Database credentials are unique to this app
- Real `.env` is never committed
- Web root points to `public`, not the repository root
- `storage` and `bootstrap/cache` are writable but source files are not world-writable

## Manual smoke tests after deployment

- Open `/up`
- Open homepage
- Open products list and product detail page
- Switch French/Arabic
- Add a product to the pack
- Complete a test checkout
- Open admin dashboard
- Upload a product image
- Update a banner/category/product and confirm storefront cache refreshes
- Open `/sitemap.xml`

## Rollback checklist

If a release fails:

1. Put the app in maintenance mode: `php artisan down`
2. Restore the previous code release
3. Restore database backup if migrations are not backward compatible
4. Run `php artisan optimize:clear`
5. Run cache commands for the restored release
6. Bring the app back: `php artisan up`
