# PetShop repository guidelines

## Build, test, and lint

Run commands from the repository root on Windows/XAMPP.

```powershell
# Install dependencies
composer install
npm install

# Local development (use separate terminals)
php artisan serve
npm run dev

# Production frontend build
npm run build

# Full PHP test suite (tests use in-memory SQLite; MySQL is not required)
php artisan test

# A single test file
php artisan test tests\Feature\HomePageTest.php

# A single test method or matching group
php artisan test --filter=test_it_shows_featured_products

# Format PHP
vendor\bin\pint

# Run the scheduler while developing reminder features
php artisan schedule:work
```

Use `php artisan migrate:fresh --seed` only when a full local database reset is intended; it deletes existing data. The normal local database is MySQL `petshop` through XAMPP, while PHPUnit overrides the connection to SQLite `:memory:` in `phpunit.xml`.

## Architecture

- This is a Laravel 12 monolith targeting PHP 8.2. HTTP routes are in `routes/web.php` and `routes/auth.php`; Laravel 12 application bootstrapping and middleware registration are in `bootstrap/app.php`.
- The application is organized around Eloquent models and migrations. The model layer covers users, pets and health records, catalog entities, carts/orders/payments, services/bookings, content/adoption, loyalty, and chat. Relationships and reusable query scopes live on the models; use eager loading for page queries.
- Controllers are thin orchestration boundaries. Form Requests own request validation. Shared application behavior belongs in a service/action class when a workflow spans multiple models or needs a transaction.
- Public pages use Blade layouts and reusable components under `resources/views`; the authenticated Breeze pages use the same branded layouts. The home page currently aggregates active catalog/services/content data in `HomeController`. Several catalog, service, blog, and cart URLs intentionally resolve to `pages.coming-soon` until their roadmap phase is implemented.
- The frontend is compiled by Vite from `resources/css/app.css` and `resources/js/app.js`. Tailwind provides the design tokens and component utilities; Livewire supplies Alpine and is started once in `app.js`. Use the existing toast store/event integration for flash or Livewire feedback.
- `config/petshop.php` contains shop-specific settings, `app/helpers.php` is autoloaded for shared helpers such as `format_vnd`, and `lang/vi` is the source for user-facing translations. The design gallery routes are local-environment-only.
- `database/seeders` provides realistic demo data used by both the application and database feature tests. Treat migrations as append-only once applied; add a new migration rather than editing an already-run migration.

## Conventions

- Keep PHP identifiers and code comments in English. Put user-facing copy in translation files and retrieve it with `__()`; do not scatter Vietnamese strings through controllers or views.
- Use backed enums in `app/Enums` for domain statuses/types and cast them in models. Prefer enum label methods and translation keys over duplicating display text.
- Models should explicitly declare `$fillable`, casts, relationships, and scopes. Do not use `$guarded = []`; sensitive fields such as a user role must not be mass assignable.
- Name every route and group routes by middleware. Use `auth`/verification middleware for account surfaces, throttle public submissions, and keep development-only routes behind a local-environment check. Admin routes belong under the `/admin` prefix and require server-side role authorization when added.
- Use Form Request classes for validation, including authentication/profile workflows and new endpoints. Controllers should not contain ad-hoc validation.
- Store all money as integer VND values, never floats. Use `format_vnd()` or the existing price Blade components for display; order items must retain the purchase-time product name and unit price.
- Use Eloquent relationships/scopes and eager loading (`with`) for filters and page data. Preserve the existing active/in-stock/featured/published scope style when adding catalog or content queries.
- Use Blade components instead of duplicating controls or cards. Follow the existing design tokens: cream surfaces, deep-green primary colors, orange accents, rounded cards, and the `container-page`/form-control patterns in `resources/css/app.css`.
- Preserve accessibility patterns already covered by the design-system tests: labelled form controls, escaped user output, useful image `alt` text, visible focus states, and localized pagination/toasts.
- Protect state-changing forms with `@csrf`; use Laravel escaping (`{{ }}`) for user data. Keep secrets in `.env` and access them through `config()` outside configuration files.
- For future checkout and booking workflows, use transactions and enforce domain invariants server-side: lock stock before decrementing, never permit negative stock, prevent booking conflicts, and authorize ownership of pet/account data with policies or gates.
- Tests are PHPUnit feature/unit tests under `tests/Feature` and `tests/Unit`, with shared setup in `tests/TestCase.php`. Follow the existing descriptive `test_...` method style and use factories/seeders rather than depending on a developer’s MySQL contents.

## Repository references

- [README.md](../README.md) documents setup, environment assumptions, and the current command list.
- [.rules/rules.md](../.rules/rules.md) is the detailed product, security, UI, and domain specification.
- [Build.md](../Build.md) is the phased implementation roadmap; check the relevant phase before implementing a planned feature.
