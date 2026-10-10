# PRD: Authenticated Multi-Guard Spider & Blade Test Harness

**Triage label:** `ready-for-agent`
**Status:** implemented (this PRD documents the delivered harness and the seams for extending it)

## Problem Statement

The `international-board` platform ships four distinct surfaces — three Filament
panels (Admin, Center, Trainer), each behind its own auth guard, plus a public
multilingual marketing/portal site under `/web`. There was **no automated test
suite at all** (`tests/Feature` and `tests/Unit` held only `.gitkeep`). A
developer changing a model, a Filament resource, a view composer, or a piece of
middleware had no fast way to know whether they had 500'd a page on a panel they
weren't looking at, or broken Arabic rendering on the public site. Combined with
the repo's aggressive boot-time strictness (no lazy loading, mandatory model
metadata), a single careless relation access can crash a page that nobody
re-opens until production.

## Solution

A read-only "spider" smoke harness that, in one `composer test` run:

- Authenticates the correct Eloquent model on each of the three guards
  (`web`/`User`, `certified_center`/`CertifiedCenter`, `trainer`/`Trainer`).
- Crawls every reachable GET route per panel plus the public `/web` routes.
- Renders the public site in both supported locales (`en`, `ar`).
- Fails the build only when a route returns **HTTP ≥ 500** (a genuine crash),
  while recording 4xx responses as informational warnings.

A developer now gets a single green/red signal that all ~122 GET endpoints across
every guard still render without server errors, including under the locale
switch. The first run already surfaced a real test-environment isolation
behaviour in `ViewServiceProvider::boot()` (see Implementation Decisions).

## User Stories

1. As a backend developer, I want a single command that loads every panel page under the right guard, so that I know I haven't 500'd a screen on a panel I wasn't editing.
2. As a backend developer, I want the harness to authenticate Admin as a `User` of type `Admin`, so that `User::canAccessPanel()` admits it and the admin panel is actually exercised.
3. As a backend developer, I want the harness to authenticate Center as an `CertifiedCenter` with an approved, non-expired accreditation request, so that `EnsureCenterIsAccredited` lets it past the gate instead of bouncing it.
4. As a backend developer, I want the harness to authenticate Trainer as an  `Trainer` with `center_id = null` and an approved, non-expired accreditation request, so that both `canAccessPanel()` and `EnsureTrainerIsAccredited` admit it.
5. As a backend developer, I want each panel crawled under its own guard rather than a shared user, so that the test reflects the real triple-guard authentication model.
6. As a frontend/Blade developer, I want every public `/web` route rendered, so that a broken partial or view composer is caught before release.
7. As an internationalisation maintainer, I want the public site crawled in both `en` and `ar`, so that locale-specific rendering and translation lookups are exercised.
8. As an i18n maintainer, I want the harness to hit `GET /web/lang/{locale}` before crawling, so that `LocaleMiddleware` and `spatie/laravel-translatable` are exercised on the path a real user takes.
9. As a developer, I want the harness to only hard-fail on HTTP ≥ 500, so that legitimate tenant-scoped 403/404 responses don't produce false build failures.
10. As a developer, I want 4xx responses recorded as warnings with their route name and status, so that I can audit whether each is expected (tenancy/authorization) or a real coverage gap.
11. As a developer, I want routes discovered programmatically from the route table, so that newly added resources and pages are crawled automatically without editing the test.
12. As a developer, I want auth/login, file-download, import/export, livewire-internal and storage routes excluded, so that the crawl only covers real pages.
13. As a developer, I want `{record}`/`{slug}`/`{serial}`/`{id}` parameters resolved from seeded fixtures, so that edit/view/show pages execute real logic rather than 404'ing immediately.
14. As a developer, I want routes whose params can't be resolved to be skipped and counted, so that coverage gaps are visible rather than silently passing.
15. As a developer, I want the harness to run on SQLite `:memory:` via `RefreshDatabase`, so that it is fast and isolated and matches `phpunit.xml`.
16. As a developer, I want all fixture seeding to be eager-load-compliant, so that the harness itself doesn't trip `preventLazyLoading`.
17. As a developer, I want the crawl to run real app code under the boot-time strictness providers, so that lazy-loading and missing-attribute violations surface as failures.
18. As a new contributor, I want a handover document describing the harness, the guard layout, and how to run the checks, so that I can extend it without reverse-engineering it.
19. As a maintainer, I want the route-coverage inventory (crawled / skipped / 4xx / 5xx per group) recorded, so that I can see at a glance what the harness covers.
20. As a maintainer, I want pre-existing static-analysis errors that the work surfaced documented separately, so that they are triaged independently of this harness.
21. As a developer, I want Pest used as the test runner, so that datasets (per-locale, per-guard) express the crawl matrix cleanly.
22. As a developer, I want the public crawl to stub Vite, so that a missing build manifest doesn't masquerade as a server error.
23. As a CI owner, I want the harness wired into the existing `composer test` flow, so that no new pipeline step is needed.

## Implementation Decisions

- **Test runner: Pest** (`pestphp/pest` + `pestphp/pest-plugin-laravel`, dev-only).
  No application packages added; Pint and PHPStan were already present. The bare
  `tests/` tree had no `TestCase.php`/`Pest.php`, so both were scaffolded
  (`TestCase` extends the framework base; `Pest.php` binds `TestCase` +
  `RefreshDatabase` to the `Feature` suite). Pest v3 has no `pest:install`
  artisan command — scaffolding is manual.
- **Guard-correct principals.** Each panel is authenticated with its own model on
  its own guard via `actingAs($model, $guard)`. The principals are seeded to pass
  each panel's real gate:
  - Admin → `User` with `type = Admin`.
  - Center → `CertifiedCenter`  + approved non-expired
    `CenterAccreditationRequest` (satisfies `EnsureCenterIsAccredited` /
    `canPerformActions()`).
  - Trainer → `Trainer` , `center_id = null`, + approved non-expired
    `TrainerAccreditationRequest` (satisfies `EnsureTrainerIsAccredited`).
- **Programmatic route discovery.** Routes are pulled from the live route table
  and partitioned by route-name prefix: `filament.admin.*` → `web`,
  `filament.center.*` → `certified_center`, `filament.trainer.*` → `trainer`,
  `web.*` → public. Excluded by substring: `.auth.`, `.logout`, `.exports.`,
  `.imports.`, `.download`, `livewire`, `storage.`.
- **Param resolution.** A resolver maps param names to seeded fixtures
  (`serial` → certification, `trainer` → trainer route key, `slug` → blog vs
  static page by route name, `id` → membership vs center by route name,
  `record` → a per-resource seeded model keyed by a route-segment → model map).
  Record seeding is best-effort and wrapped so a factory that can't satisfy its
  dependencies skips the route instead of erroring.
- **Failure contract.** Hard failure = HTTP ≥ 500 only. 4xx are warnings.
  Rationale: tenant-scoped record routes correctly return 403/404 for ids that
  don't belong to the authenticated principal; asserting `< 400` there would be
  false positives.
- **Public view-globals isolation (discovered).** `ViewServiceProvider::boot()`
  early-returns when `runningInConsole()` is true, so its
  `View::share('navigationPages', …)` (and `appSettings`, `socialLinks`,
  `currentLocale`, `availableLocales`) never run under tests — every public Blade
  page 500s on an undefined `$navigationPages`. This is **test-only**; real HTTP
  requests are not in console, so production is unaffected. The harness mirrors
  the provider's populated branch in a helper so the public crawl renders real
  Blade. A follow-up option (out of scope here) is to gate that provider on
  request context rather than `runningInConsole()`.
- **Locale coverage.** Public crawl is a Pest dataset over `['en', 'ar']`
  (from `config('app.available_locales')`); each iteration hits the locale-switch
  route first, then crawls.

## Testing Decisions

- **What a good test asserts here:** external, observable behaviour — the HTTP
  status of a real GET request through the full middleware/render stack — not
  internal implementation. The harness deliberately does not assert on markup or
  internals; its contract is "no endpoint crashes", which stays valid as the UI
  evolves.
- **Seams used (highest available):** the public HTTP kernel via Laravel's
  `$this->get()` test client, and the framework auth layer via `actingAs(…, guard)`.
  No new seams were introduced — both are existing, top-level entry points.
  Route discovery uses the framework router as its seam, so the matrix expands
  automatically with the app.
- **Modules exercised:** all three Filament panels, the public web controllers
  (`Http/Controllers/Web/**`), `LocaleMiddleware`, the accreditation gate
  middleware, the view composer layer, and — transitively — the services,
  repositories, models, and Blade views each route touches, all under the
  boot-time strictness providers.
- **Prior art:** none in-repo (this is the first test). It follows standard
  Laravel/Pest feature-test conventions (`RefreshDatabase`, factories, the HTTP
  test client) so future feature tests can mirror it.
- **Fixtures:** model factories already existed for all 20 models and are reused;
  the harness only adds the orchestration that wires gate-passing principals and
  param fixtures together.

## Out of Scope

- Changing `ViewServiceProvider`'s `runningInConsole()` gate (the harness works
  around it in test infra instead).
- Asserting page content/markup correctness, accessibility, or performance.
- POST/PUT/DELETE flows, form submissions, and Livewire action round-trips — the
  harness is GET-only by design.
- Eliminating the expected 4xx warnings (tenant-scoped 404s, authorization 403s,
  the `serial`-bound certification show route).

## Further Notes

- **Coverage at delivery:** admin 69 / center 22 / trainer 15 / public 16 (×2
  locales) ≈ **122 GET routes crawled, 0 server errors.**
- The "deprecated" flag in Pest output is an unrelated PHP 8.5
  `PDO::MYSQL_ATTR_*` constant deprecation, not from harness code.
- Full engineering handover lives at `docs/plans/agent-state.md`.
- **Follow-up work completed after the initial harness:**
  - PHPStan brought to **0 errors** by enabling Larastan (it was installed but
    not wired into `phpstan.neon`) and fixing one `env()`-outside-config call.
  - Tenant-scoped `{record}` routes now seed principal-owned records → 4xx
    warnings dropped 20 → 6 (all remaining are authorization/tenancy by design).
  - The `serial` resolver now uses `accredited_serial_number`.
  - **Two real production 500s found and fixed** — a phantom
    `Certification::documentType` referenced in `TrainerRepository` (eager-load)
    and `centers/_details.blade.php` (render); crashed any trainer/center profile
    that had certifications, in all environments.
- Remaining optional improvement: seed a center-linked trainee (via a
  certification) so the center Trainees `whereHas` scope resolves in-scope.
