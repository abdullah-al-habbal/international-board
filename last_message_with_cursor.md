All audit tasks are done. Here is a concise summary.
## Why 5 files were deleted (app still works)
| File | Reason | Impact |
|------|--------|--------|
| `AccreditationRequestController` | Returned `view('')`, **not in** `routes/web.php` | None |
| `UserController` | Empty stub, not routed | None |
| `ApplicationSettingController` (×2) | Empty stubs, not routed | None |
| `certification_list.blade.php` | Never included; wrong fields on `Certification` | None |
All **14 public routes** and **Filament** panels are unchanged. Verified: `php artisan route:list --path=web` and `php artisan view:cache` both succeed.
---
## Fixes in this pass
**Config / PHP**
- Added [`app/Support/LocaleConfig.php`](app/Support/LocaleConfig.php) — fixes Intelephense P1006 on `LocaleController`
- [`ViewServiceProvider`](app/Providers/ViewServiceProvider.php) — defaults `navigationPages = []` and `appSettings = collect()` before DB
- [`StaticPageController`](app/Http/Controllers/Web/StaticPage/StaticPageController.php) — SEO from `Str::limit(content)`; extends `Controller`
**Blade logic**
- [`certifications/_result.blade.php`](resources/views/web/certifications/_result.blade.php) — uses `$certification->document_code` (removed invalid `documentCode` relation)
- [`certifications/_not_found.blade.php`](resources/views/web/certifications/_not_found.blade.php) — back button `btn-main`
- [`centers/_filters.blade.php`](resources/views/web/centers/_filters.blade.php) — clear only when `search` is filled
- [`pages/show.blade.php`](resources/views/web/pages/show.blade.php) — cleaned structure
**UI — card glow**
- `.card-glow` in [`style.css`](public/assets/website/css/style.css) and [`style-rtl.css`](public/assets/website/css/style-rtl.css)
- Applied on trainer/center/blog lists, center/trainer detail cards, certification result/not-found cards
- Subtle glow on page header block (`.page-title.bg-2 .block`)
---
## Docs updated
- [`claude.md`](claude.md) — “Deleted files (safe)”, `LocaleConfig`, `card-glow` UI rules
- [`docs/report.md`](docs/report.md) — post-audit fixes section
- [`docs/specs/01-architecture.md`](docs/specs/01-architecture.md) — `LocaleConfig` + ViewServiceProvider defaults
- [`docs/specs/05-backlog.md`](docs/specs/05-backlog.md) — second-pass items checked off
---
## Optional follow-ups (documented, not implemented)
- Home `about_section` still uses lang checklist instead of DB `content`
- Centers index has no country filter UI (only search)
- PHPUnit feature tests for web listings
Functionality for home, static pages, certification verify, centers, trainers, blog, and locale switching remains intact with the fixes above.
