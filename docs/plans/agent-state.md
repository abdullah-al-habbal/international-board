# Agent State — Multi-Guard Spider & Blade Test Harness

_Status: complete. All harness tests green; 0 server errors across the crawled surface._

## What was built

A read-only "spider" smoke harness (Pest) that authenticates into each of the
three Filament panels on its own guard and crawls every reachable GET route,
plus a public multilingual Blade crawl. The single hard assertion per group is
**no route returns HTTP ≥ 500**; 4xx responses are collected as warnings (record
routes legitimately 403/404 on tenant-foreign ids).

### Files added
- `tests/TestCase.php` — base test case (was missing).
- `tests/Pest.php` — binds `TestCase` + `RefreshDatabase` to the `Feature` suite.
- `tests/Support/Spider.php` — seeds the 3 guard principals + public domain
  records, enumerates GET routes by panel/guard, resolves route params, and
  mirrors the public view globals (see "Test-env finding" below).
- `tests/Feature/UnifiedPlatformSpiderTest.php` — 4 tests: admin, center,
  trainer panels + public site (datasets `en`, `ar`).

### Dependencies added (dev only)
- `pestphp/pest`, `pestphp/pest-plugin-laravel` (`composer.json` / `composer.lock`).
  No application packages added; Pint/PHPStan were already installed.

## Stack / architecture (orientation)
- Laravel 12, PHP 8.5, Filament 4. Local + test DB = SQLite (`:memory:` in tests
  per `phpunit.xml`).
- **Three panels, three guards** (`config/panels.php`, `config/auth.php`):
  - Admin `/admin` — guard `web`, model `User` (gate: `UserType::Admin`).
  - Center `/center` — guard `certified_center`, model `CertifiedCenter`
    (middleware `EnsureCenterIsAccredited`; gate: `is_active` + approved
    non-expired `CenterAccreditationRequest`).
  - Trainer `/trainer` — guard `trainer`, model `Trainer` (middleware
    `EnsureTrainerIsAccredited`; gate: `is_active` + `center_id = null` +
    approved non-expired `TrainerAccreditationRequest`).
- Public site under `/web` (`routes/web.php`), multilingual via `LocaleMiddleware`
  + `spatie/laravel-translatable`. Locales: `en`, `ar`.
- Boot-time strictness: `EloquentServiceProvider` (no lazy loading / no missing
  attributes / no silent discards), `ArchitectureServiceProvider` (`$table` +
  `$primaryKey` required). The crawl runs real app code under these, so any
  violation surfaces as a 500.

## How to run local checks
```bash
composer test                                                   # full suite (Pest)
./vendor/bin/pest tests/Feature/UnifiedPlatformSpiderTest.php   # just the spider
composer fix                                                    # Pint format
./vendor/bin/phpstan analyse app --memory-limit=1G             # static analysis (app-scoped)
```

## Route coverage inventory (latest run)
| Group   | Guard              | Crawled | 5xx | 4xx (warnings) |
|---------|--------------------|--------:|----:|---------------:|
| admin   | `web`              | 69      | 0   | 1              |
| center  | `certified_center` | 22      | 0   | 2              |
| trainer | `trainer`          | 15      | 0   | 1              |
| public  | (none) × en/ar     | 16      | 0   | 1 (per locale) |

**Total ≈ 122 GET routes crawled, 0 server errors.** Tenant-scoped `{record}`
routes are now seeded with records **owned by the authenticated principal**
(`Spider::seedOwnedRecords()`), so center/trainer edit/view pages render in-scope
(200) instead of 404. This dropped total 4xx warnings from 20 to 6.

Excluded by design (not page routes): `*.auth.*`, logout, `*.exports.*`,
`*.imports.*`, `*.download`, livewire internals, `storage.*`.

### Remaining 4xx warnings (all expected, not bugs)
- **`admin … center-type-requests.view` 403** — authorization/policy gating.
- **`trainer … trainer-accreditation-requests.create` 403** — accreditation gate.
- **`web.health` 403** — health-token check (`config('services.health.token')`).
- **`center … trainees.view` / `.edit` 404** — the Trainees resource scopes via
  `whereHas('certifications', …)` rather than a direct `center_id`; the seeded
  owned trainee isn't linked through a certification, so it falls out of scope.
  Tightening this (seed a trainee linked via a certification to the center) is a
  possible future improvement; left as a documented, expected 404.

## Test-env finding (worth knowing, not fixed — out of scope)
`App\Providers\ViewServiceProvider::boot()` early-returns under
`runningInConsole()`, so its `View::share('navigationPages', …)` (and
`appSettings`, `socialLinks`, `currentLocale`, `availableLocales`) never run in
tests. Without those globals, **every public Blade page 500s** on an undefined
`$navigationPages` (`resources/views/components/header/primary_nav.blade.php`).
This is **test-only** — real HTTP requests are not in console, so production is
unaffected. The harness replicates the provider's populated branch via
`Spider::sharePublicViewGlobals()` so the public crawl exercises real Blade.
(If broader console-side rendering is ever needed, consider gating that provider
on request context rather than `runningInConsole()`.)

## Bugs found by the spider and fixed
The richer owned-record fixtures gave the public-facing trainer/center actual
certifications, which surfaced **two real production 500s** — both from a phantom
`Certification::documentType` relationship that was never built (the
`certifications` table has no `document_type_id`; only a `document_code` string).
Under the boot-time strictness providers these throw in **all** environments
(not just tests), so any trainer/center *with certifications* crashed its page:

1. `app/Repositories/Trainer/TrainerRepository::findActiveByKey()` eager-loaded
   `certifications.documentType` → `RelationNotFoundException` (500) on the public
   trainer profile. Fixed: eager-load `['country', 'certifications']`. The view
   never used the nested relation.
2. `resources/views/web/centers/_details.blade.php` rendered
   `$certification->documentType->name` → `MissingAttributeException` (500) on the
   public center profile. Fixed: render the existing fallback
   (`Certification #{id}`) directly; the `@if ($certification->documentType)`
   guard could never be true.

## PHPStan: enabled Larastan + fixed env() (was 14 errors → now 0)
`phpstan.neon` included only the baseline — **Larastan was installed but not
enabled**, so plain PHPStan couldn't resolve Eloquent magic (static
`Model::where/find/create/pluck`, dynamic model `@property` access). That caused
all 14 errors (many similar ones were already papered into
`phpstan-baseline.neon`). Root-cause fix: added
`vendor/nunomaduro/larastan/extension.neon` to the `includes` of `phpstan.neon`.
That resolved all 14 and surfaced one genuine Larastan rule violation —
`env('HEALTH_TOKEN')` called outside the config dir in `HealthCheckController`
(returns null when config is cached). Fixed by adding `services.health.token` to
`config/services.php` and reading `config('services.health.token')`.

`./vendor/bin/phpstan analyse app --memory-limit=1G` → **0 errors.** PHPStan is
scoped to `app/`, so the new `tests/` files are not analysed (Pint formats them).

## Verification performed
- `composer fix` → Pint clean on new files.
- `./vendor/bin/pest tests/Feature/UnifiedPlatformSpiderTest.php` → 5 passing
  (4 tests; public parametrised ×2). "deprecated" flag is an unrelated PHP 8.5
  `PDO::MYSQL_ATTR_*` constant deprecation, not from this code.
- `composer test` → green.
