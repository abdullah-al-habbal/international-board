# Backlog

Prioritized tasks from the project audit. Status updated after implementation pass (2026-05-20).

## P0 — Critical

- [x] Fix `ViewServiceProvider` — use `config('app.available_locales')`
- [x] Blog index: `blog_list` partial, card grid, square images
- [x] Remove `ArchitectureServiceProvider` invalid `*Model` naming check

## P1 — High (UI / nav)

- [x] Add blog to `primary_nav` and footer
- [x] Fix nav `active` with `routeIs('web.*')` patterns
- [x] Unify home `blog_section` with `blog_list` partial
- [x] CSS `.img-square` for listing thumbnails; apply to trainer/center/blog lists

## P2 — Medium (cleanup)

- [x] Delete orphan `certification_list.blade.php`
- [x] Delete broken `AccreditationRequestController` (unrouted)
- [x] Delete empty web controller stubs
- [x] Footer lorem → `web.footer.description` (en/ar)

## P3 — Low (second audit pass)

- [x] `LocaleConfig` helper; fix Intelephense/config typing
- [x] `ViewServiceProvider` default `navigationPages` / `appSettings`
- [x] Fix `certifications/_result` `document_code` display bug
- [x] `StaticPageController` SEO from content; extend `Controller`
- [x] `.card-glow` CSS on listing and detail cards
- [x] Certification not-found back button → `btn-main`
- [x] Centers filter clear button (search only)
- [ ] Add PHPUnit feature tests for web listing pages *(skipped per product request)*
- [ ] Optional: public certification browse catalog (product decision)

## Future ideas

- [x] Home `about_section`: render DB `content` with checklist fallback
- [x] Centers index: country filter dropdown (`getFilterCountries()` + `_filters`)
- [ ] CMS-editable footer via `ApplicationSetting`
- [ ] Home page inline trainer/center carousels using existing list partials
- [ ] Consolidate `img-standard` vs `img-square` documentation in theme README
- [ ] Register `BlogPostService` as singleton for consistency

## Out of scope (documented)

- Filament admin resource changes from parallel git work
- New certification directory without product sign-off
