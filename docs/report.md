# Project audit report

**Date:** 2026-05-20  
**Scope:** Full codebase scan — models, Filament, routes, public Blade UI, providers.

## Executive summary

The platform is a well-structured Laravel 12 + Filament 4 app with clear service/repository layering and three Filament panels. Public listing pages for trainers and centers follow a consistent card-grid pattern; the blog index and navigation lagged behind. Several dead or broken web artifacts were removed or fixed during this pass.

## Critical / broken (resolved)

| Issue | Location | Resolution |
|-------|----------|------------|
| Empty view name | `AccreditationRequestController` | Controller deleted (not routed) |
| Wrong config key | `ViewServiceProvider` — `app.locales` | Fixed to `available_locales` |
| Model naming guard | `ArchitectureServiceProvider` | Removed invalid `*Model` suffix check |
| Orphan blade with wrong fields | `certification_list.blade.php` | Deleted |
| Empty controller stubs | `Web\User`, `Web\ApplicationSetting` | Deleted |

## High — UI consistency (resolved)

| Issue | Resolution |
|-------|------------|
| Blog index used legacy `.post-item` markup | `blog_list` partial + card grid |
| Blog missing from nav/footer | Added with `routeIs('web.blog.*')` active state |
| Listing images circular on index pages | `.img-square` for listings; `.img-standard` kept for profile avatars |
| Home blog section duplicated markup | Reuses `blog_list` partial |
| Footer lorem ipsum | Replaced with `web.footer.description` (en/ar) |
| Blog show back button style | Aligned to `btn-main` |

## Medium — by design / documented

| Item | Notes |
|------|-------|
| Certifications index | Search/verify only — no public catalog grid |
| `certification_list` component | Removed; would need new API if catalog is requested later |
| Filament trainer color `emerald` | Mapped in `ResolvesFilamentColor` |

## Low — remaining / watch

| Item | Notes |
|------|-------|
| Nav active on nested routes | Broadened with `routeIs()` for blog, certifications, centers, trainers |
| Home page | Does not list trainers/centers inline (only stats + blog section) — acceptable |
| `BlogPostService` | Not registered as singleton (Laravel auto-wires — OK) |
| Git in-progress Filament admin edits | Out of scope for this pass |

## Filament inventory

- **Admin:** 20 resources  
- **Center:** 7 resources (scoped to authenticated center)  
- **Trainer:** 3 resources (scoped to authenticated trainer)  
- **No Client panel** — see `04-filament-client.md` for Center + Trainer docs

## Public web inventory

- **Controllers:** 8 active (Home, Locale, StaticPage, Certification, CertifiedCenter, Trainer, Blog, HealthCheck)
- **Views:** 21 under `resources/views/web/`
- **Components:** 15 under `resources/views/components/`

## Post-audit fixes (second pass)

| Issue | Location | Resolution |
|-------|----------|------------|
| Intelephense `config()` null on `in_array` | `LocaleController` | `App\Support\LocaleConfig` helper |
| Undefined `$navigationPages` without DB | `ViewServiceProvider` | Default `[]` / empty `appSettings` before table checks |
| Wrong relation in verify result | `certifications/_result.blade.php` | `document_code` string (removed `documentCode` relation) |
| Static page SEO excerpt | `StaticPageController` | `Str::limit` on content; extends `Controller` |
| Card visual polish | `style.css` / blades | `.card-glow` on listings, details, certification cards |
| Centers filter clear button | `centers/_filters` | Clear only when `search` filled (removed unused `country_id` check) |

## Why deleted files do not break the app

See [claude.md](../claude.md) section **Deleted files (safe)**. Grep shows no imports or routes referencing removed controllers or `certification_list`.

## Recommendations

1. Add feature tests for `web.blog.index` and trainer/center index pagination.
2. Consider `ApplicationSetting` for footer text if CMS editing is required.
3. If a public certification directory is needed, add repository method + controller action + new list partial (do not revive deleted `certification_list` as-is).
