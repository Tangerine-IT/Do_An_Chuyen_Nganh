# Project: Pet Shop Website (PetShop)

You are a senior full-stack engineer working on this Laravel project. Priorities: correct, simple, maintainable code and a polished, easy-to-use UI. This is a graded university project that is judged on novelty and quality, so the signature features and the UI must be complete and well finished.

## 1. Stack (do not change without asking)
- PHP 8.2+ (do NOT use syntax that requires a version newer than 8.3), Laravel (the version in composer.json), MySQL via XAMPP (database name: petshop).
- Frontend: Blade + Tailwind CSS + Alpine.js + Livewire (for instant product filtering, cart, and search). Built with Vite.
- Charts: Chart.js. Maps: Leaflet + OpenStreetMap.
- Auth: Laravel Breeze (Blade). Authorization: `role` column (customer, staff, admin) + Policies/Gates + middleware.
- Tests: PHPUnit or Pest, whichever the project already uses. Formatting: Laravel Pint.
- Environment: Windows + XAMPP. Run the app with `php artisan serve`. Commands must work on Windows; avoid bash-only syntax when possible.

## 2. Project commands
- Install PHP deps: `composer install`
- Install JS deps: `npm install`
- Dev servers: `php artisan serve` and `npm run dev` (two terminals)
- Migrate and seed: `php artisan migrate:fresh --seed`
- Tests: `php artisan test`
- Format: `./vendor/bin/pint`
- Production build: `npm run build`
- Scheduler during dev (vaccination reminders): `php artisan schedule:work`

## 3. Workflow
1. Read the relevant files and follow the existing conventions BEFORE editing. Never guess the structure.
2. Large tasks (multiple files, migrations, new features): give a short plan (3-7 bullets), then implement. Small tasks: just do them.
3. Keep changes within the scope of the request. No drive-by refactors.
4. After changes, run `php artisan test` and `./vendor/bin/pint`. If anything fails, read the error, fix it, and rerun until green. If you cannot run them, say so and explain why.
5. Never invent Laravel or package methods. If unsure, check the docs or ask.
6. After finishing a feature, summarize: what changed, which files, and how to verify manually (URL, steps).

## 4. Laravel structure and conventions
- Keep controllers thin. Put business logic in `app/Services/*` or Action classes (e.g. `PlaceOrderAction`, `BookingService`).
- Validate with Form Requests, not inside controllers.
- Use Eloquent relationships and eager loading (`with()`) to avoid N+1 queries. Use query scopes for filters (`scopeInStock`, `scopeForPet`).
- Models: declare `$fillable`, `casts`, and relationships. Never use `$guarded = []`.
- Routes: always named (`->name()`), grouped by middleware (`auth`, `role:admin`). Admin routes use the `/admin` prefix.
- Migrations: create with `php artisan make:migration`, include foreign keys and indexes. NEVER edit a migration that has already run; add a new one.
- Seeders and factories must produce realistic data (pet products, brands, services) so demos look good.
- Naming: classes PascalCase, variables and methods camelCase, tables snake_case plural, columns snake_case. Code and comments in English; user-facing strings in Vietnamese via `lang/vi` files or `__()`, never scattered hardcoded text.
- Use PHP backed Enums for statuses (OrderStatus, BookingStatus, PetSpecies, etc.).

## 4b. Domain rules (important)
- Money: store VND as unsigned big integers (never floats). Display through a formatting helper (e.g. `12.500.000 ₫`).
- Checkout: use a DB transaction, lock stock rows (`lockForUpdate`), decrement stock, and NEVER allow negative stock. Store the price at purchase time in `order_items` (do not reference the current product price).
- Service booking: check for time conflicts per staff member and service duration inside a transaction; only allow slots within working hours and in the future.
- Pet profiles: every pet belongs to one user; users may only view or edit their own pets (Policy).
- Product recommendations: filter by species, breed, age, and weight; warn when `ingredients` contains something the pet is allergic to; always show the reason for a recommendation.
- Health record book: store `record_date` and `next_due_date`; a daily Scheduler command sends reminder emails ahead of the due date (e.g. 7 days) without sending duplicates.
- Coupons: validate expiry, usage limit, and minimum order value on the server.
- AI chatbot: call the AI API from the server only (key in `.env`), never from the browser. Include relevant product data and the user's pet profile in the prompt; do not send unnecessary personal data (email, password, address). Add rate limiting and graceful handling of API errors.
- Payments (VNPay/MoMo): sandbox only. Verify the signature on the callback and make order status updates idempotent.

## 5. UI (must look polished and consistent)
- Mobile-first and responsive; check 375px, 768px, and 1280px widths.
- Design with intent, not default styling: a custom palette (suggestion: warm cream background, deep green primary, orange accent), a font with character (e.g. Nunito or Quicksand for headings), soft rounded corners, large pet photography, generous whitespace.
- Reuse Blade components (`<x-product-card>`, `<x-button>`, `<x-pet-badge>`) instead of duplicating HTML.
- Every screen handles loading, empty, error, and success states. Use toast notifications for feedback.
- Accessibility: `alt` text on images, labels on form fields, sufficient color contrast, keyboard navigation.
- Performance: lazy-load images, paginate lists, avoid loading unnecessary data.
- Admin: tables with search, pagination, and a confirmation step before deleting.

## 6. Security
- Never commit `.env`, API keys, or passwords. Use `config()` instead of calling `env()` outside config files.
- Prevent XSS (use `{{ }}`, never `{!! !!}` with user data), CSRF (`@csrf`), and SQL injection (Eloquent or parameterized query builder).
- Enforce authorization on the server for EVERY action (Policy/Gate); hiding a button is not enough.
- Uploads: restrict type and size, randomize filenames, store via the storage disk (`php artisan storage:link`).
- Hash passwords with Laravel's default (bcrypt). Rate-limit login and the chatbot.

## 7. Testing
- Write tests for critical logic: checkout (stock, prices), coupons, booking (conflict prevention), authorization, allergy-aware recommendations.
- Never delete or weaken a test just to make it pass. If a test is genuinely wrong, explain why before changing it.

## 8. Never do
- Do not change the stack or add large packages without stating the reason.
- Do not run `migrate:fresh`, delete data, `git reset --hard`, or `git push --force` without asking first.
- Do not leave `dd()`, `dump()`, `console.log`, dead code, or vague TODOs behind.
- Do not hardcode URLs, keys, or machine-specific absolute paths.

## 9. Communication
- Reply in Vietnamese by default: concise and to the point. Code, identifiers, and code comments stay in English.
- When a request is ambiguous or has several valid approaches, ask one short question, or pick the most reasonable option and state the assumption.
- When debugging, explain the root cause first, then fix it.
