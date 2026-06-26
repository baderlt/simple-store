# Wany Bio Laravel Store

Wany Bio is a Laravel e-commerce store with a public storefront, cart/checkout flow, admin product management, banners, categories, discounts, Arabic/French localization, SEO metadata, sitemap support, and optimized product image uploads.

## Main stack

- Laravel 12
- PHP 8.2+
- MySQL/MariaDB recommended for production
- Vite, Tailwind CSS, Alpine.js
- Database-backed cache, session, and queue configuration for shared hosting

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan serve
```

For active frontend development:

```bash
npm run dev
```

## Useful commands

```bash
php artisan test
php artisan route:list
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Production deployment

Read the production guide before deploying:

[docs/production-deployment.md](docs/production-deployment.md)

Minimum production reminders:

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Set `APP_URL` to the HTTPS store URL
- Run `composer install --no-dev --prefer-dist --optimize-autoloader`
- Run `npm ci && npm run build`, or upload prebuilt `public/build`
- Run `php artisan migrate --force`
- Run `php artisan storage:link`
- Cache config/routes/views after deployment
- On shared hosting, keep the database queue running with a cron job

## Health check

The application exposes Laravel's built-in health endpoint:

```text
/up
```

Use this for uptime monitoring or load balancer health checks.

## Localization

The storefront supports French as the default language and Arabic with RTL presentation. Translation files live in:

- `lang/fr`
- `lang/ar`
- `lang/en`

Arabic-specific content fields are optional and should fall back to French/default content when empty.

## SEO

The storefront includes:

- Product meta fields and fallback SEO generation
- Open Graph and Twitter metadata
- Product JSON-LD
- Breadcrumb JSON-LD
- XML sitemap at `/sitemap.xml`
- Robots rules in `public/robots.txt`

## Image uploads

Product images are optimized through GD before storage. Relevant environment variables:

```dotenv
PRODUCT_IMAGE_MAX_WIDTH=1600
PRODUCT_IMAGE_MAX_HEIGHT=1600
PRODUCT_IMAGE_QUALITY=82
```

Uploaded public files require:

```bash
php artisan storage:link
```

## Testing before release

Run:

```bash
php artisan test
npm run build
php artisan route:list
```

Manual smoke tests:

- Homepage loads
- Products and categories load
- Arabic/French switch works
- Product image gallery works
- Add to pack works
- Checkout creates an order
- Admin can create/update products, categories, banners, and discounts
- `/sitemap.xml` opens
- `/up` returns HTTP 200
