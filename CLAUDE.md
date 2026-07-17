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

`tests/Unit` and `tests/Feature` are currently empty (only `.gitkeep`) — there is no existing test suite to mirror; phpunit is wired up and ready.

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

## Knowledge graph (graphify)

A knowledge graph exists at `graphify-out/`. For codebase questions prefer `graphify query "<question>"`, `graphify path "<A>" "<B>"`, `graphify explain "<concept>"` over raw grep. After modifying code run `graphify update .` to keep it current (AST-only, no API cost). See `AGENTS.md`.
