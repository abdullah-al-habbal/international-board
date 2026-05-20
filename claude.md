# Board — Agent Guide

National Certification Authority platform: issue and verify certifications, manage accredited centers and trainers, and run accreditation workflows via Filament admin panels.

## Stack

| Layer | Technology |
|-------|------------|
| Runtime | PHP 8.2+ |
| Framework | Laravel 12 |
| Admin UI | Filament 4 |
| i18n | Spatie Laravel Translatable (`en`, `ar`) |
| Excel | Maatwebsite Excel |
| Frontend (public) | Blade + Bootstrap theme in `public/assets/website/` |

## Quick start

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
php artisan migrate
php artisan serve
```

| Surface | URL |
|---------|-----|
| Public site | `/web` (root `/` redirects here) |
| Admin panel | `/admin` |
| Center panel | `/center` |
| Trainer panel | `/trainer` |
| Health check | `/web/health` |

## Directory map

```
app/
  Enums/              # AccreditationStatus, CenterStatus, UserType, etc.
  Filament/
    Admin/Resources/  # 20 admin resources
    Center/Resources/   # 7 center-scoped resources
    Trainer/Resources/  # 3 trainer-scoped resources
  Http/Controllers/Web/   # Public site controllers
  Models/             # 20 Eloquent models
  Repositories/       # Data access (9 repositories)
  Services/           # Business logic
  Policies/           # Authorization
resources/views/
  home_page.blade.php
  layouts/master.blade.php
  web/                # Public pages (blog, centers, trainers, certifications)
  components/         # Shared sections, partials, header, footer
lang/en/, lang/ar/    # web.php, filament.php, app.php
config/app.php        # available_locales: en, ar
config/panels.php     # Filament panel paths and guards
routes/web.php        # Public routes
routes/admin.php      # Filament (required from web.php)
```

## Architecture

**Request flow (public web):** `routes/web.php` → `Http\Controllers\Web\*` → `Services\*` → `Repositories\*` → `Models\*` → Blade views.

**Filament:** Panel providers auto-discover resources under `app/Filament/{Admin,Center,Trainer}/`. Center and Trainer panels scope queries to the authenticated account.

See [docs/specs/01-architecture.md](docs/specs/01-architecture.md) for providers, SEO, observers, and view composition.

## Deleted files (safe — no runtime impact)

These were removed in the documentation/UI pass because they were **never routed** or **never included** in views:

| File | Why removed |
|------|-------------|
| `Web/AccreditationRequest/AccreditationRequestController` | Returned `view('')`; not in `routes/web.php` |
| `Web/User/UserController` | Empty stub |
| `Web/ApplicationSetting/*Controller` | Empty stubs (2 files) |
| `components/sections/certification_list.blade.php` | Orphan; wrong model fields (`title`, `code` vs `accredited_serial_number`) |

All 14 public `web.*` routes and Filament panels remain functional.

## Locale configuration

Use [`App\Support\LocaleConfig`](app/Support/LocaleConfig.php) for type-safe locale lists (avoids `config()` mixed/null in static analysis):

- `LocaleConfig::availableLocales()` — `list<string>`
- `LocaleConfig::isAvailable($locale)` — bool
- `LocaleConfig::defaultLocale()` — string

Used by `LocaleController`, `LocaleMiddleware`, and `ViewServiceProvider`.

## Conventions

- `declare(strict_types=1);` on PHP files
- `final` controllers and most services
- Filament v4: resources delegate to `Schemas/*Form.php`, `Tables/*Table.php`, `Pages/*`
- Models use `#[Scope]` attribute scopes where applicable
- Policies via `#[UsePolicy]` or `AuthServiceProvider`
- Do not rename models to end in `Model` — legacy `ArchitectureServiceProvider` rule was removed

## Internationalization

- Locales: `config('app.available_locales')` → `['en', 'ar']`
- Switch: `GET /web/lang/{locale}` → `web.locale`
- Public strings: `lang/{locale}/web.php`
- Filament: `lang/{locale}/filament.php` + `bezhansalleh/filament-language-switch` on panels
- RTL: `style-rtl.css` when `app()->getLocale() === 'ar'`
- Translatable models: `Country`, `DocumentType`, `BlogPost`, `StaticPage`

## Public routes

| Route name | URI | Controller |
|------------|-----|------------|
| `web.home` | `/web` | `HomeController` |
| `web.locale` | `/web/lang/{locale}` | `LocaleController` |
| `web.pages.show` | `/web/pages/{slug}` | `StaticPageController@show` |
| `web.certifications.index` | `/web/certifications` | Verify landing |
| `web.certifications.search` | `/web/certifications/search` | Serial lookup |
| `web.certifications.show` | `/web/certifications/{serial}` | Certification detail |
| `web.centers.index` | `/web/centers` | Center directory |
| `web.centers.show` | `/web/centers/{id}` | Center profile |
| `web.trainers.index` | `/web/trainers` | Trainer directory |
| `web.trainers.evaluation` | `/web/trainers/evaluation` | Evaluation page |
| `web.trainers.show` | `/web/trainers/{trainer}` | Trainer profile |
| `web.blog.index` | `/web/blog` | Blog listing |
| `web.blog.show` | `/web/blog/{slug}` | Blog post |
| `web.health` | `/web/health` | Health check |

## Web UI rules

Listing pages (trainers, centers, blog) must follow the same pattern:

1. `page_header` partial for title/subtitle
2. `section.section` > `container` > optional filters > `row` > list partial
3. Bootstrap **card** grid: `col-lg-4 col-md-6 col-sm-6 mb-5`, `card card-glow h-100`
4. **Square** listing images: class `img-square` (1:1, `object-fit: cover`) — not circular
5. **Glow border**: class `card-glow` on listing/detail cards (soft shadow + accent border in `style.css`)
6. Profile/show pages may use `img-standard` (circular avatar) on detail views
7. Shared list partials: `components.sections.{trainer,center,blog}_list`
8. Pagination: `components.partials.pagination` with `$items` paginator

Certifications public index is **verify-by-serial only** (no browse grid).

**Home about block:** [`about_section`](resources/views/components/sections/about_section.blade.php) uses CMS `content` from slug `about-us` when present; otherwise lang checklist.

**Centers index filters:** search + `country_id` select (countries with active centers via `CertifiedCenterService::getFilterCountries()`).

## Filament panels

| Panel | Path | Guard | User model |
|-------|------|-------|------------|
| Admin | `/admin` | `web` | `User` |
| Center | `/center` | `certified_center` | `CertifiedCenter` |
| Trainer | `/trainer` | `trainer` | `Trainer` |

Center/Trainer panels require accreditation middleware before full access. See [docs/specs/03-filament-admin.md](docs/specs/03-filament-admin.md) and [docs/specs/04-filament-client.md](docs/specs/04-filament-client.md).

## Shared view data

`ViewServiceProvider` shares globally (defaults before DB):

- `navigationPages` — `[]` then active static pages when `static_pages` table exists
- `appSettings` — empty collection then key-value from `application_settings`
- `currentLocale`, `availableLocales` (via `LocaleConfig`)

## Documentation index

| File | Contents |
|------|----------|
| [docs/report.md](docs/report.md) | Audit findings and severity |
| [docs/specs/01-architecture.md](docs/specs/01-architecture.md) | Layers, providers, cross-cutting concerns |
| [docs/specs/02-data-model.md](docs/specs/02-data-model.md) | Models, relations, enums |
| [docs/specs/03-filament-admin.md](docs/specs/03-filament-admin.md) | Admin panel resources |
| [docs/specs/04-filament-client.md](docs/specs/04-filament-client.md) | Center & Trainer panels |
| [docs/specs/05-backlog.md](docs/specs/05-backlog.md) | Prioritized tasks and status |

## Do not change casually

- Observer cache keys: `home_stats_certifications`, `home_stats_trainers`, `home_stats_centers` (invalidated on model changes)
- Filament resource folder layout and `Schema` delegation pattern
- Accreditation gate logic in `AccreditationGateService` without product sign-off
- Unrelated in-progress Filament admin edits on other branches

## Useful commands

```bash
php artisan route:list --path=web
php artisan filament:optimize
./vendor/bin/pint
./vendor/bin/phpstan analyse
```
