# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 12 on **PHP 8.5** (pinned in `composer.json` `config.platform`), Filament 4 admin panels, Vite 7 + Tailwind 4 for the public site. Multilingual via `spatie/laravel-translatable`. Default DB for local/dev is SQLite (`database/database.sqlite`); tests use an in-memory SQLite.

## Commands

```bash
composer dev            # run server + queue:listen + vite + pail concurrently (main dev loop)
composer test           # config:clear then `php artisan test`
php artisan test --filter=SomeTest          # run a single test/class
php artisan test tests/Feature/FooTest.php  # run a single file
composer fix            # ./vendor/bin/pint (format)
./vendor/bin/pint --test                    # check formatting without writing
./vendor/bin/phpstan analyse app --memory-limit=1G   # static analysis (larastan, baseline in phpstan-baseline.neon)
npm run build           # production asset build
composer ci-check:quick # pint --test + phpstan level 1 (fast pre-push gate)
composer ci-check:full  # install + pint --test + phpstan + test --stop-on-failure
composer check-all      # full local: pint, config/route/view cache, optimize, test
```

Tests live in `tests/Feature` and `tests/Unit` (Pest, `RefreshDatabase` via `tests/Pest.php`, in-memory SQLite). `UnifiedPlatformSpiderTest` crawls all three panels plus the public site in every locale and is the broadest regression net.

## Architecture

### Three Filament panels, separated by auth guard
Panels are **Admin**, **Center**, **Trainer** (`app/Filament/{Admin,Center,Trainer}`), each registered by its own `PanelProvider` in `app/Providers/Filament/`. Panel config (id, path, color, guard, password broker, resource namespaces) lives in **`config/panels.php`** — read it before touching a panel; providers pull all settings from there.

- **Admin** (`/admin`, guard `web`): the `User` model. Access gated in `User::canAccessPanel()` — requires `PanelId::Admin` + `UserType::Admin`.
- **Center** (`/center`, guard `certified_center`): the `CertifiedCenter` model.
- **Trainer** (`/trainer`, guard `trainer`): the `Trainer` model, plus `EnsureTrainerIsAccredited` auth middleware.

Guards/providers/brokers are defined in `config/auth.php`. The same domain (e.g. Certifications, Trainers) appears as a Resource in multiple panels with different scoping — when changing behavior, check all panels that expose that resource.

Filament 4 layout: each Resource is a directory with `Pages/`, `Schemas/` (form schemas), `Tables/`, and sometimes `RelationManagers/` — not a single class file.

### Layered domain code (not fat controllers/models)
Request flow for the public site: `Http/Controllers/Web/**` → **Service** (`app/Services/<Domain>/`) → **Repository** (`app/Repositories/<Domain>/`) → Model. Services and repositories are registered as **singletons** and constructor-inject their model:
- `app/Providers/RepositoryServiceProvider.php` — binds repositories.
- `app/Providers/ServiceRegistrationProvider.php` — binds domain + stats services.

When adding a new repository/service, register it in the matching provider. Repositories hold query logic (filters, pagination, eager-loads); services hold orchestration; controllers stay thin.

**Eloquent Resolvers** (`app/Eloquent/Resolvers/<Domain>/`) are single-purpose query builders for exports/reports (e.g. `CertifiedCenterActiveCentersExportResolver::query()`). Exports live in `app/Exports/`, imports in `app/Imports/`.

### Enforcement service providers (boot-time strictness)
Registered in `bootstrap/providers.php`. These change framework behavior globally — expect them to fail loudly:
- `EloquentServiceProvider` — `preventLazyLoading`, `preventAccessingMissingAttributes`, `preventSilentlyDiscardingAttributes`. **No lazy loading**: eager-load relations (`->with(...)`) or queries throw.
- `ArchitectureServiceProvider` — every retrieved model must declare `$table` and `$primaryKey` or it throws.
- Other providers: `Query`, `Performance`, `Security`, `Validation`, `Observer` (model observers), `Macro`, `View`.

### Public web vs. admin
- `routes/web.php` — public multilingual site under `/web` prefix (redirect from `/`). `routes/admin.php` exists separately; Filament panels self-register their routes.
- `LocaleMiddleware` (appended to `web` group, alias `locale`) handles language; switch via `GET /web/lang/{locale}`. Translations in `lang/`.
- Scheduled: `CleanupStorageCommand` daily at 03:00 (`bootstrap/app.php`).

## Conventions

- Every PHP file starts with `declare(strict_types=1);`. Domain classes are `final`. Full type hints expected (phpstan is in CI).
- Enums in `app/Enums/` (e.g. `PanelId`, `UserType`) — use them instead of string/magic literals.
- Run `pint` before committing; phpstan must stay green against the baseline.

## Deployment

Single branch: `production`. Deployed to Hostinger via SSH git pull.

```bash
# Standard deployment (safe, no data loss)
git commit -m "Add new feature"
git push origin production
```

### ⚠️ Production holds live data, and there are no backups

Production is **not** an empty environment. As of 2026-08-26 it holds thousands of
certifications and trainees, plus the trainers and centers they belong to. No backups
are taken, by explicit decision — so any dropped table is **permanently** lost.

Because of that:

- **Commit-message keywords do nothing.** `[migrate:fresh]`, `[destructive]`,
  `[reset-db]` and `[fresh]` are no longer wired to anything. A push can only ever run
  `php artisan migrate --force`. CI logs a warning if it sees one of these keywords, and
  ignores it.
- **Destructive migrations require a deliberate manual run:** GitHub Actions → CI/CD
  Pipeline → Run workflow → check `force_migrate_fresh`. This drops every table and
  re-seeds. There is no undo.
- Write migrations to be additive and reversible. Never reach for `migrate:fresh` to fix
  a schema mistake on production.

### Timezone

`APP_TIMEZONE=Asia/Damascus` must be set in every environment. Accreditation validity is
evaluated against the operators' local day; with the framework default (`UTC`) the stack
runs three hours behind the operators and expires credentials early. The deploy script
fails fast if `APP_TIMEZONE` is missing from the production `.env`.

CI/CD pipeline: `.github/workflows/ci.yml`

## Knowledge graph (graphify)

A knowledge graph exists at `graphify-out/`. For codebase questions prefer `graphify query "<question>"`, `graphify path "<A>" "<B>"`, `graphify explain "<concept>"` over raw grep. After modifying code run `graphify update .` to keep it current (AST-only, no API cost). See `AGENTS.md`.

---

## Feature Index

### Domain Models & Relationships

| Model | Table | Key Relationships |
|---|---|---|
| `User` | `users` | `certifications()` MorphMany |
| `Trainer` | `trainers` | `country()` BelongsTo, `trainerRole()` BelongsTo(TrainerRole), `center()` BelongsTo(CertifiedCenter), `specializations()` BelongsToMany, `certifications()` MorphMany, `documentTypes()` HasMany(TrainerDocumentType), `accreditationRequests()` HasMany, `financialRequests()` MorphMany |
| `CertifiedCenter` | `certified_centers` | `certifications()` MorphMany, `trainers()` HasMany(Trainer), `country()` BelongsTo, `documentTypes()` HasMany(CertifiedCenterDocumentType), `approvedDocumentTypes()` HasMany (status=approved), `accreditationRequests()` HasMany, `financialRequests()` MorphMany |
| `Certification` | `certifications` | `creator()` MorphTo (User/Trainer/CertifiedCenter), `documentable()` MorphTo (DocumentType/TrainerDocumentType/CertifiedCenterDocumentType), `country()` BelongsTo, `trainee()` BelongsTo, `assignedTrainer()` BelongsTo(Trainer) |
| `Trainee` | `trainees` | `country()` BelongsTo, `certifications()` HasMany |
| `Country` | `countries` | — |
| `Specialization` | `specializations` | `trainers()` BelongsToMany (pivot: `specialization_trainer`) |
| `TrainerRole` | `trainer_roles` | `trainers()` HasMany(Trainer). Uses `HasTranslations` (translatable: `name`). Admin-managed lookup; `trainers.trainer_role_id` is nullable with `nullOnDelete()` |
| `DocumentType` | `board_document_types` | `certifications()` MorphMany. Uses `HasTranslations` (translatable: `name`) |
| `TrainerDocumentType` | `trainer_document_types` | `trainer()` BelongsTo, `certifications()` MorphMany, `reviewer()` BelongsTo(User). Uses `HasTranslations` (translatable: `name`) |
| `CertifiedCenterDocumentType` | `certified_center_document_types` | `certifiedCenter()` BelongsTo, `certifications()` MorphMany, `reviewer()` BelongsTo(User). Uses `HasTranslations` (translatable: `name`) |
| `TrainerAccreditationRequest` | `trainer_accreditation_requests` | `trainer()` BelongsTo, `reviewedBy()` BelongsTo(User) |
| `CenterAccreditationRequest` | `center_accreditation_requests` | `certifiedCenter()` BelongsTo, `reviewer()` BelongsTo(User) |
| `FinancialRequest` | `financial_requests` | `requestable()` MorphTo (CertifiedCenter|Trainer), `agentPerson()` BelongsTo(AgentPerson), `currency()` BelongsTo(Currency) |
| `AgentPerson` | `agent_persons` | `centerFinancialRequests()` HasMany, `trainerFinancialRequests()` HasMany |
| `Currency` | `currencies` | `financialRequests()` HasMany. Uses `HasTranslations` (translatable: `name`, `symbol`). Admin-managed lookup; reference rows come from `config/currencies.php` |
| `Membership` | `memberships` | — |
| `BlogPost` | `blog_posts` | — |
| `StaticPage` | `static_pages` | — |
| `ContactMessage` | `contact_us_messages` | — |
| `ApplicationSetting` | `application_settings` | — |

### Filament Resources by Panel

#### Admin Panel (`/admin`)
| Resource | Path | Notes |
|---|---|---|
| `UserResource` | `app/Filament/Admin/Resources/Users/` | Admin users |
| `CertifiedCenterResource` | `app/Filament/Admin/Resources/CertifiedCenters/` | Manage centers; has RelationManagers for Trainers, ApprovedDocumentTypes, FinancialRequests |
| `TrainerResource` | `app/Filament/Admin/Resources/Trainers/` | Manage trainers; has FinancialRequests RelationManager |
| `CertificationResource` | `app/Filament/Admin/Resources/Certifications/` | View/manage all certifications |
| `TraineeResource` | `app/Filament/Admin/Resources/Trainees/` | Manage trainees |
| `CountryResource` | `app/Filament/Admin/Resources/Countries/` | Manage countries |
| `SpecializationResource` | `app/Filament/Admin/Resources/Specializations/` | Manage specializations |
| `TrainerRoleResource` | `app/Filament/Admin/Resources/TrainerRoles/` | Manage trainer roles (admin only — Center/Trainer panels can assign but not administer) |
| `DocumentTypeResource` | `app/Filament/Admin/Resources/DocumentTypes/` | Board document types |
| `TrainerDocumentTypeResource` | `app/Filament/Admin/Resources/TrainerDocumentTypes/` | Approve/reject trainer doc types |
| `CertifiedCenterDocumentTypeResource` | `app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/` | Approve/reject center doc types |
| `TrainerAccreditationRequestResource` | `app/Filament/Admin/Resources/TrainerAccreditationRequestResource/` | Approve/reject trainer accreditation |
| `CenterAccreditationRequestResource` | `app/Filament/Admin/Resources/CenterAccreditationRequests/` | Approve/reject center accreditation |
| `TrainerFinancialRequestResource` | `app/Filament/Admin/Resources/TrainerFinancialRequests/` | View trainer financial requests |
| `CertifiedCenterFinancialRequestResource` | `app/Filament/Admin/Resources/CertifiedCenterFinancialRequests/` | View center financial requests |
| `PaymentAgentPersonResource` | `app/Filament/Admin/Resources/PaymentAgentPersons/` | Payment agent persons |
| `CurrencyResource` | `app/Filament/Admin/Resources/Currencies/` | Manage currencies (admin only) |
| `MembershipResource` | `app/Filament/Admin/Resources/MembershipResource/` | Memberships |
| `BlogPostResource` | `app/Filament/Admin/Resources/BlogPosts/` | Blog posts |
| `StaticPageResource` | `app/Filament/Admin/Resources/StaticPages/` | Static pages |
| `ApplicationSettingResource` | `app/Filament/Admin/Resources/ApplicationSettings/` | App settings |
| `ContactMessageResource` | `app/Filament/Admin/Resources/ContactMessageResource/` | Contact messages |

**Widgets:** `StatsOverview`, `AccreditationChart`, `CertificationChart`

#### Center Panel (`/center`)
| Resource | Path | Notes |
|---|---|---|
| `CertificationResource` | `app/Filament/Center/Resources/Certifications/` | Center-scoped certifications |
| `TrainerResource` | `app/Filament/Center/Resources/Trainers/` | Manage trainers under this center |
| `TraineeResource` | `app/Filament/Center/Resources/Trainees/` | Manage trainees |
| `CertifiedCenterDocumentTypeResource` | `app/Filament/Center/Resources/CertifiedCenterDocumentTypes/` | CRUD for center's doc types (create/view/edit/delete) |
| `CenterAccreditationRequestResource` | `app/Filament/Center/Resources/CenterAccreditationRequests/` | Submit accreditation requests |
| `CenterFinancialRequestResource` | `app/Filament/Center/Resources/CenterFinancialRequests/` | **Read-only** financial history (index + view only; no create/edit/delete) |
| `CenterProfilePage` | `app/Filament/Center/Pages/CenterProfilePage.php` | Edit own profile |

#### Trainer Panel (`/trainer`)
| Resource | Path | Notes |
|---|---|---|
| `CertificationResource` | `app/Filament/Trainer/Resources/Certifications/` | Trainer-scoped certifications |
| `TrainerDocumentTypeResource` | `app/Filament/Trainer/Resources/TrainerDocumentTypes/` | CRUD for trainer's doc types (create/view/edit/delete) |
| `TrainerAccreditationRequestResource` | `app/Filament/Trainer/Resources/TrainerAccreditationRequests/` | Submit accreditation requests |
| `TrainerFinancialRequestResource` | `app/Filament/Trainer/Resources/TrainerFinancialRequests/` | **Read-only** financial history (index + view only; no create/edit/delete) |
| `TrainerProfilePage` | `app/Filament/Trainer/Pages/TrainerProfilePage.php` | Edit own profile |

### Public Website Routes

| Route | Controller | View |
|---|---|---|
| `GET /web` | `HomeController@index` | `web.home.index` |
| `GET /web/lang/{locale}` | `LocaleController@set` | — (redirect) |
| `GET /web/pages/{slug}` | `StaticPageController@show` | `web.pages.show` |
| `GET /web/certifications` | `CertificationController@index` | `web.certifications.index` |
| `GET /web/certifications/search` | `CertificationController@search` | `web.certifications.search` |
| `GET /web/certifications/{serial}` | `CertificationController@show` | `web.certifications.show` |
| `GET /web/centers` | `CertifiedCenterController@index` | `web.centers.index` |
| `GET /web/centers/{id}` | `CertifiedCenterController@show` | `web.centers.show` |
| `GET /web/trainers` | `TrainerController@index` | `web.trainers.index` |
| `GET /web/trainers/evaluation` | `TrainerController@evaluation` | `web.trainers.evaluation` |
| `GET /web/trainers/{trainer}` | `TrainerController@show` | `web.trainers.show` |
| `GET /web/blog` | `BlogController@index` | `web.blog.index` |
| `GET /web/blog/{slug}` | `BlogController@show` | `web.blog.show` |
| `GET /web/memberships` | `MembershipIndexController@index` | `web.memberships.index` |
| `GET /web/memberships/{id}` | `MembershipShowController@show` | `web.memberships.show` |
| `POST /web/contact` | `StoreContactMessageController@store` | — (redirect) |
| `GET /web/health` | `HealthCheckController@index` | — (JSON) |

### Services & Repositories

| Domain | Service | Repository |
|---|---|---|
| Accreditation | `AccreditationGateService`, `TrainerAccreditationApprovalService` | — |
| AccreditationRequest | `AccreditationRequestService` | `AccreditationRequestRepository` |
| ApplicationSetting | `ApplicationSettingService` | `ApplicationSettingRepository` |
| Blog | `BlogPostService` | `BlogPostRepository` |
| Certification | `CertificationService`, `CertificationExportHandler` | `CertificationRepository` |
| CertifiedCenter | `CertifiedCenterService` | `CertifiedCenterRepository` |
| Contact | `ContactMessageService` | — |
| Csv | `CsvExportHandler`, `CsvImportHandler` | — |
| Home | `HomeService` | — |
| Membership | `MembershipService` | — |
| Seo | `SeoService` | — |
| Stats | `StatsService`, `CenterStatsService` | — |
| StaticPage | `StaticPageService` | `StaticPageRepository` |
| Trainer | `TrainerService` | `TrainerRepository` |
| User | `UserService` | `UserRepository` |

### Enums

| Enum | Cases | Used For |
|---|---|---|
| `AccreditationStatus` | `Pending`, `Approved`, `Rejected`, `UnderReview` | Accreditation requests |
| `CenterStatus` | `Active`, `Inactive`, `Pending`, `Suspended` | Center status |
| `DocumentTypeRequestStatus` | `Pending`, `Approved`, `Rejected` | Document type approval |
| `PanelId` | `Admin`, `Center` | Panel identification |
| `SettingType` | `Text`, `Number`, `Boolean`, `Json`, `Email`, `Phone`, `Url`, `Html` | Application settings |
| `UserType` | `Admin`, `Client` | User roles |
| `ChartColors` | Various color constants | Dashboard charts |

### Observers

| Observer | Model | Behavior |
|---|---|---|
| `CertificationObserver` | `Certification` | Clears `home_stats_certifications` cache |
| `TrainerObserver` | `Trainer` | Clears `home_stats_trainers` cache |
| `CertifiedCenterObserver` | `CertifiedCenter` | Auto-generates `accreditation_number` (IBVTQ), clears cache |
| `TrainerAccreditationRequestObserver` | `TrainerAccreditationRequest` | Prevents duplicate active requests, blocks time overlaps, auto-stamps reviewed_by/at, updates trainer period on approve/reject |
| `CenterAccreditationRequestObserver` | `CenterAccreditationRequest` | Prevents duplicate active requests, blocks time overlaps, auto-stamps reviewed_by/at, activates/deactivates center on approve/reject |
| `FinancialRequestObserver` | `FinancialRequest` | Enforces the money invariants on every write: `total_payment > 0`, `amount_paid >= 0`, `amount_paid <= total_payment`. Throws `DomainException` |
| `CurrencyObserver` | `Currency` | Refuses to delete a currency referenced by a financial request (mirrors the `restrict` FK with a translated message) |

### Middleware

| Middleware | Purpose |
|---|---|
| `EnsureTrainerIsAccredited` | Blocks trainer panel actions if not accredited; allows dashboard + accreditation request routes |
| `EnsureCenterIsAccredited` | Blocks center panel actions if not accredited; allows dashboard + accreditation request routes |
| `LocaleMiddleware` | Sets app locale from session; falls back to default |

### Policies

`CertifiedCenterPolicy`, `CertificationPolicy`, `CountryPolicy`, `DocumentTypePolicy`, `TraineePolicy`, `UserPolicy` — all in `app/Policies/`.

### Blade Templates (Public Site)

```
resources/views/web/
├── certifications/   index, search, show, _not_found, _result, _search_section, _statistics
├── centers/          index, show, _details, _filters, _logo, _statistics
├── trainers/         index, show, evaluation, _avatar, _filters, _profile, _statistics
├── blog/             index, show
├── memberships/      index, show
├── pages/            show, _content
├── contact/          _form
```

### Translation Files

- `lang/en/app.php`, `lang/ar/app.php` — shared app labels
- `lang/en/web.php`, `lang/ar/web.php` — public site strings
- Other locale dirs: `ar/`, `en/`

### Key Model Behaviors

- **Trainer** — `accreditation_number` auto-generated (IBVTQ) via `TrainerObserver`.
- **CertifiedCenter** — `accreditation_number` auto-generated (IBVTQ) via `CertifiedCenterObserver`. `notes` field for admin-written notes (read-only in Center panel, public on site).
- **Certification** — `document_code` (`CERT-YYYYMMDD-XXXX`), `accredited_serial_number` (`SN-YYYYMMDD-XXXXXX`), and `accreditation_number` (IBVTQ) auto-generated via `CertificationObserver`.
- **Certification** uses polymorphic `creator` (User/Trainer/CertifiedCenter) and `documentable` (DocumentType/TrainerDocumentType/CertifiedCenterDocumentType).
- **DocumentType**, **TrainerDocumentType**, **CertifiedCenterDocumentType** all use `HasTranslations` trait with `$translatable = ['name']` for multilingual name storage (JSON in DB).
- **Money** — never do arithmetic on `total_payment` / `amount_paid` with PHP floats. They are `DECIMAL(12,2)` and Eloquent's `decimal:2` cast returns strings; every authoritative calculation goes through `App\Support\Money` (exact integer minor units, fixed-scale decimal strings out). `FinancialRequest::$remaining_amount` is the single source of truth and returns a string; the live form preview calls the same `Money::subtract()`, so the UI is never a second implementation. `amount_paid <= total_payment` is enforced by `FinancialRequestObserver`, not only by the Filament forms.
- **Money in Filament** — reuse `App\Filament\Components\{MoneyInput,MoneyColumn,MoneyEntry}` and the compositions in `App\Filament\FinancialRequests\FinancialRequestFields`; do not re-inline `->money(fn ($record) => $record->currency?->code ?? 'USD')`. `MoneyInput` carries the Alpine `$money` mask plus `stripCharacters(',')`, and `dir="ltr"` so amounts stay left-to-right under Arabic. `MoneyColumn` / `MoneyEntry` read `$record->currency`, so any table or infolist showing them **must** eager-load `currency` — on a Filament v4 `RelationManager` that means `->modifyQueryUsing(...)` on the table (a static `getEloquentQuery()` override is never called), and Laravel only raises the lazy-load violation once a query returns more than one row, so test such tables with 2+ records.
- **File uploads**: Trainer `avatar` → `trainers/avatars/`, CertifiedCenter `logo` → `centers/logos/`, User `avatar` → `users/avatars/`, all on the `public` disk. Every upload point is restricted to `acceptedFileTypes(['image/jpeg','image/png','image/webp'])` with `maxSize(2048)` — SVG is excluded deliberately (it can carry script and is served same-origin). Do not widen this back to `image/*`.
- **Orphaned uploads**: Filament writes a new file on every replacement and never removes the old one. `CleanupStorageCommand` sweeps unreferenced files under those three directories, with a 24h grace period so an in-flight upload is never deleted.