# Localization audit report

## Scope scanned
- Blade views, including storefront, auth, profile, cart, checkout, orders, admin, emails, layouts, and pagination overrides.
- Application PHP classes, including controllers, middleware, models, mailables, and request/auth flows.
- Public JavaScript used for product interactions and discount administration.
- Existing language assets in `lang/en`, `lang/fr`, and `lang/ar`.

## Untranslated or incorrectly localized text found
- Flash messages and JSON response messages in cart, checkout, admin product/category/banner/order/discount/settings/profile controllers were hardcoded in French or English.
- New-order email subject was hardcoded in French.
- Admin authorization error text was hardcoded in middleware.
- Layout cart drawer labels and empty/loading states were hardcoded in French.
- Public JavaScript alerts, prompts, validation messages, loading labels, and product-search messages were hardcoded in French.
- Several translation calls used ungrouped keys such as `status_pending`, `Dashboard`, and pagination/auth literals without corresponding language files.
- `lang/fr/messages.php` had a structural conflict where `messages.home` returned an array while layout code expected a string.
- Arabic RTL support existed in the admin layout but not consistently in the public and guest layouts.
- The locale switch route and middleware did not accept the existing English language resources.

## Files modified
- Controllers/middleware/mailables now use `__('group.key')` for user-facing flash, JSON, exception, and email subject text.
- Public/guest layouts now set `dir="rtl"` for Arabic and `ltr` otherwise.
- The cart drawer in the main layout now uses translation keys for labels and exposes localized JavaScript strings through `window.appTranslations`.
- Public JavaScript now reads alert/prompt/button/validation strings from `window.appTranslations` instead of embedding French strings.
- Language files were added for `admin`, `auth`, `cart`, `checkout`, `discounts`, `home`, `mail`, `pagination`, `product`, `products`, `status`, and `validation` in English, French, and Arabic.
- JSON language files were added for legacy string-style keys used by Breeze/vendor pagination and existing ungrouped translation calls.

## Missing language keys added
- Admin operations: products, categories, banners, discounts, orders, settings, profile, and authorization errors.
- Storefront cart/checkout operations: stock errors, cart mutations, drawer labels, order submission, and stock log notes.
- Product JavaScript: sharing, clipboard, quantity validation, and checkout redirection states.
- Discount JavaScript: required fields, date ordering, product selection, and discount-value validation.
- Auth/pagination/status/home groups required by existing views and models.

## Potential follow-up localization issues
- Some seed/demo product and category names are still stored as French content because they are database content rather than UI chrome. If multilingual demo data is required, add translatable columns or locale-aware seed fixtures.
- A small number of legacy Blade templates still use string-style `__('Literal')` calls for framework/vendor auth and pagination copy. JSON language files now cover these keys, but future cleanup could migrate them to grouped keys.
- The existing Arabic static HTML replacement middleware remains in place for legacy hardcoded content not yet migrated from older templates. It should be removed only after a full template-by-template migration confirms no legacy static strings remain.
