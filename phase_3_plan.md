# Phase 3 Implementation Plan

This document outlines the future work required to finalize the application's frontend integration and optimization. It encompasses the pending features identified in the Phase 3 preview, as well as verification steps for the recently added architectural components.

## 1. Navigation Dynamic Population
*   **Objective:** Dynamically generate header and footer navigation links based on active `StaticPage` records.
*   **Implementation Steps:**
    *   Utilize the `navigationPages` variable (shared globally via `ViewServiceProvider`).
    *   Update the `layouts.master` (or header/footer partials) to loop through `$navigationPages` instead of using hardcoded links.
    *   Ensure routes for these dynamic pages use the `web.pages.show` route with the appropriate `slug`.

## 2. Locale Switcher Component with Session Persistence
*   **Objective:** Allow users to toggle the site language between English (en) and Arabic (ar) from the frontend UI.
*   **Implementation Steps:**
    *   Create a route to handle locale switching (e.g., `/lang/{locale}`).
    *   Create a `LocaleController` that validates the requested locale against `config('app.locales')`.
    *   Set the selected locale in the session (`session()->put('locale', $locale)`).
    *   Create or update a middleware (`SetLocale`) to read the session value and apply it using `app()->setLocale()`.
    *   Add a UI dropdown or button in the header (using the global `$availableLocales` and `$currentLocale` variables) to switch languages.

## 3. SEO Meta Tags Service
*   **Objective:** Inject dynamic SEO meta tags (Title, Description, OpenGraph, etc.) for each page.
*   **Implementation Steps:**
    *   Create an `SeoService` to handle default and page-specific meta values.
    *   Update the `layouts.master` to include a `@yield('seo_meta')` or pass the `SeoService` instance.
    *   For dynamic pages (e.g., `Certifications`, `Centers`, `Trainers`, `StaticPages`), inject specific meta descriptions, canonical URLs, and OpenGraph images based on the viewed record.

## 4. Cache Optimization for Statistics Queries
*   **Objective:** Improve home page load times by caching expensive aggregate queries (e.g., total certifications, trainers, and centers).
*   **Implementation Steps:**
    *   Update `HomeService::getStatistics()`.
    *   Wrap the repository count methods in `Cache::remember()` or `Cache::rememberForever()`.
    *   Define appropriate cache tags (if using Redis/Memcached) or standard cache keys (e.g., `home_stats_certifications`).
    *   Set up Eloquent model observers (`CertificationObserver`, `TrainerObserver`, `CertifiedCenterObserver`) to invalidate or update these cache keys when records are created, updated, or deleted.

## 5. Blog Module (Future Expansion)
*   **Objective:** Fully implement the blog section currently stubbed out.
*   **Implementation Steps:**
    *   Create the `BlogPostRepository` and `BlogPostService`.
    *   Create the Filament Admin Resource to manage `BlogPost` entries.
    *   Create frontend routes, a `BlogController`, and Blade views (`index`, `show`).
    *   Re-enable the `blog_section` in the `home_page.blade.php`.

---

## ✅ Verification Checklist of Completed Prerequisites
The following items have been implemented and must be maintained/verified during Phase 3:
- [x] `StaticPageService::getBySlug()` and `StaticPageRepository` implemented.
- [x] `Certification` model scopes confirmed.
- [x] Model Accessors (`Trainer::getAvatarUrlAttribute`, `CertifiedCenter::getLogoUrlAttribute`) implemented.
- [x] `RepositoryServiceProvider` bindings confirmed as singletons.
- [x] `ViewServiceProvider` registered and sharing global data.
- [x] `AppServiceProvider` customized with `Paginator::useBootstrapFive()`.
- [x] `BlogPost` model stub created.
- [x] Storage symlink created (`php artisan storage:link`).
