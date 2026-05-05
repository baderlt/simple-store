# Generic Multi-Purpose Storefront Refactor Blueprint

## 1) Target architecture
- **Domain layers**: Catalog, Content, Storefront, Marketing, Localization.
- **Settings as CMS**: all public text/media/colors/SEO in DB.
- **Homepage builder**: database-driven sections with ordering and per-section JSON config.
- **Theme engine**: CSS variables resolved from settings + theme tokens.
- **Composable UI**: Blade components per section/card/button variant.

## 2) Database structure
### Settings table (existing, extended by convention)
- `key` (unique), `value` (text), `type` (`text|image|number|json|html|color|script`)
- Key namespaces:
  - `brand.*` (`brand.store_name`, `brand.logo`, `brand.favicon`)
  - `theme.*` (`theme.active`, `theme.primary_color`, `theme.radius`)
  - `seo.*` (`seo.default_title`, `seo.default_description`)
  - `contact.*`, `social.*`, `map.*`, `footer.*`, `announcement.*`
  - `home.section.{section}.title`

### New table: `homepage_sections`
- `key`, `name`, `is_enabled`, `position`, `layout`, `settings(json)`.
- Example `settings`: CTA labels, image IDs, style variant, per-locale copy.

## 3) Migration plan
1. Add `homepage_sections` table.
2. Seed default generic settings + default sections.
3. Backfill old pharmacy keys into new namespaces.
4. Remove deprecated hardcoded defaults in Blade views.

## 4) Admin settings architecture
- `StoreSettingsService` as single read/write API + caching.
- Settings groups rendered from schema config (field metadata-driven forms).
- Live preview endpoint returns merged settings + theme tokens.
- Media manager for logo/favicons/banner assets.

## 5) Homepage builder architecture
- Source of truth: `homepage_sections` ordered by `position`.
- Controller resolves active sections then renders `components/storefront/sections/{key}.blade.php`.
- Unknown/missing sections skipped safely.
- Admin drag/drop updates `position` via AJAX bulk endpoint.

## 6) Blade component structure
- `resources/views/components/storefront/`
  - `section-shell.blade.php`
  - `buttons/{primary,secondary,ghost}.blade.php`
  - `cards/{product,category,testimonial}.blade.php`
  - `sections/{hero_slider,categories,featured_products,...}.blade.php`

## 7) Dynamic theme system
- `config/storefront.php` defines available themes.
- Runtime CSS variables in `<style>:root{--c-primary:...}</style>` from settings.
- Theme preset fallback map per theme key.
- Dark mode switch toggles `data-theme="dark"` and token set.

## 8) SEO architecture
- Per-page + per-locale meta fields in DB.
- Canonical URLs, OpenGraph, Twitter cards from settings.
- JSON-LD organization + product schema.
- Sitemap includes content pages/blog/categories/products.

## 9) Performance architecture
- Cache settings forever + bust on update.
- Cache homepage section payload.
- Eager-load products/images/discounts to avoid N+1.
- Responsive image generation (WebP/AVIF), `loading="lazy"`.
- Defer non-critical JS and initialize Swiper only for visible sections.

## 10) Refactoring strategy for current `home.blade.php`
1. Extract each major block into section component file.
2. Replace pharmacy text with settings keys (`hero_title`, `hero_subtitle`, etc.).
3. Replace pharmacy-specific map marker/title with generic store contact data.
4. Move all style literals into theme tokens/utilities.
5. Keep current HTML semantics, then iterate section-by-section.

## 11) SaaS readiness
- Add `tenant_id` to settings/sections (future migration) for multi-tenant separation.
- Repository interfaces for content retrieval.
- Feature flags per tenant for optional modules.

## 12) Recommended packages
- `spatie/laravel-medialibrary` (media management)
- `spatie/laravel-translatable` (localized DB content)
- `spatie/laravel-permission` (RBAC)
- `spatie/laravel-responsecache` (public page caching)
- `intervention/image` (transformations)

## 13) UI/UX admin improvements
- Widgetized dashboard (sales/content/system health).
- Global search over products/categories/pages/settings.
- Bulk actions + keyboard shortcuts.
- Inline validation and autosave drafts for section editors.

## 14) Implementation roadmap
- **Phase 1**: foundations (settings service, homepage sections schema, seeds, theme config).
- **Phase 2**: extract homepage into section components + dynamic rendering.
- **Phase 3**: admin homepage builder (toggle/order/layout/content).
- **Phase 4**: multi-language + RTL + localized SEO.
- **Phase 5**: theme presets + visual editor + performance hardening.
