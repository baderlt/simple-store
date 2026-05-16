# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview
Laravel 12.x e-commerce application ("simple-store") with PHP ^8.2. Uses Vite for frontend asset compilation, Tailwind CSS 3.x, Alpine.js for interactivity. Supports multilingual UI (French, Arabic) via session-based locale switching.

## Common Commands
- **Setup**: `composer setup` (installs deps, copies .env, generates key, migrates, builds frontend)
- **Local dev**: `composer dev` (concurrently runs Laravel server, queue listener, log tail, Vite dev)
- **Frontend**: `npm run dev` (Vite dev), `npm run build` (production build)
- **Tests**: `composer test` (runs PHPUnit via `php artisan test`), single test: `php artisan test --filter=TestName`
- **Linting**: `./vendor/bin/pint` (Laravel Pint for PHP code style)

## Architecture
### Backend
Standard Laravel 12 layout with key custom components:
- **Models**: Product, Category, Order, OrderItem, Discount, Setting, Banner, HomepageSection, StockLog
- **Admin controllers** (`app/Http/Controllers/Admin/`): Dashboard, Product, Category, Order, Discount, Setting, Banner
- **Frontend controllers**: Home, Product, Category, Cart, Checkout, Order, Promotion
- **Middleware**: `AdminMiddleware` (checks `User->is_admin`), `SetLocale`, `TranslateStaticHtml`
- **Services**: `StoreSettingsService` (cached settings management)
- **Helpers**: `settings()` global helper (app/Helpers/helpers.php)

### Routing
- Public: `/`, `/products`, `/categories`, `/cart`, `/checkout`, `/promotions`, `/about`
- Auth: Laravel Breeze (login, register, password reset)
- Admin: `/admin` prefix, protected by `auth` + `admin` middleware

### Settings
Stored in `settings` DB table, cached indefinitely via `StoreSettingsService`. Access with `settings('key', 'default')`.

### Multilingual
Supported locales: `fr`, `ar` (lang/ directory). Locale set via session (`/lang/{locale}` route). Default fallback: `en`.

### Frontend
- Vite entry points: `resources/css/app.css`, `resources/js/app.js`
- Blade templates with Alpine.js, no Inertia/React/Vue
- Session-based cart (no login required for checkout)
- AJAX cart endpoints: `/cart/ajax/*`

### Database
Default: SQLite (`:memory:` for testing). Session, cache, queue use database driver.
