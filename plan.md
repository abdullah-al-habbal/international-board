# Bug Fix Plan — International Board Website

## Overview

Five bugs identified during RTL/Arabic and settings handling review. Fix order is dependency-aware.

---

## BUG-01 · Critical — ApplicationSetting Infinite Recursion

**File:** `app/Models/ApplicationSetting.php`

**Root cause:** The `value()` Attribute accessor calls `$this->getTypedValue()`, which internally accesses `$this->value` — this re-enters the same Attribute accessor. Stack overflow on any page load once settings records exist.

**Impact path:** `ViewServiceProvider::boot()` → `ApplicationSetting::all()->pluck('value', 'key')` → each record accesses `->value` → `getTypedValue()` → `$this->value` → infinite recursion.

**Fix:** Pass the raw database value through the Attribute callback directly instead of re-accessing `$this->value`.

---

## BUG-02 · Critical — Hero Section Completely Invisible in Arabic (RTL)

**Files:**
- `public/assets/website/css/style-rtl.css` (syntax error + direction conflict)
- `public/assets/website/js/script.js` (Slick config is correct but contradicted by CSS)
- `public/assets/website/css/style.css` (`overflow-x: hidden` clips off-screen slides)

**Root causes:**
1. **Missing `}` on line 33 of `style-rtl.css`** — The `.hero-slider` rule block lacks a closing brace, causing `.hero-slider .slider-item { direction: rtl !important; }` to be consumed as CSS error recovery. Slider items lose RTL text direction.
2. **`direction: ltr !important` contradicts `rtl: true`** — CSS forces LTR direction on `.hero-slider` with `!important`, overriding the `dir="rtl"` attribute set by Slick. Slick's `rtl: true` option then positions slides incorrectly (off-screen), and `overflow-x: hidden` clips them entirely.

**Fix:**
1. Add the missing `}` between the two rule blocks.
2. Remove the conflicting `direction: ltr !important` from slider containers — let Slick manage direction via its `dir` attribute and `rtl: true` option.

---

## BUG-03 · High — Footer Social Links Hardcoded With Dead Translation Fallback

**Files:**
- `resources/views/components/footer/main_footer.blade.php`
- `app/Providers/ViewServiceProvider.php`
- `lang/en/filament.php`, `lang/ar/filament.php`

**Issues:**
1. `{{ __('filament.labels.social_links') ?? 'Social Links' }}` — `__()` never returns `null`, it returns the raw key string. The `??` fallback never activates; the heading shows "filament.labels.social_links".
2. Social links are hardcoded as `$appSettings['facebook_url']`, `$appSettings['twitter_url']`, `$appSettings['linkedin_url']` — not scalable, throws if keys are missing.
3. Translation key `filament.labels.social_links` doesn't exist in any lang file.

**Fix:**
1. Add `'labels' => ['social_links' => 'Social Links']` to `filament.php` lang files (en + ar).
2. In `ViewServiceProvider`, decode the `social_links` JSON setting and share it as `$socialLinks`.
3. Replace hardcoded links in footer with a `@foreach` loop over `$socialLinks`.
4. Use `$appSettings->get('key')` instead of `$appSettings['key']` to avoid runtime exceptions.

---

## BUG-04 · Medium — Missing `filament.labels.social_links` Translation Key

**Files:** `lang/en/filament.php`, `lang/ar/filament.php`

Both files lack the `labels.social_links` key. Bundled with BUG-03 fix.

---

## BUG-05 · Low — Footer Copyright Hardcoded in English

**File:** `resources/views/components/footer/main_footer.blade.php`

`"All rights reserved."` is a literal English string — never translated.

**Fix:** Add `footer.copyright` key to `web.php` lang files and use `__()` with parameters.

---

## Fix Execution Order

| Order | Bug | Rationale |
|-------|-----|-----------|
| 1 | BUG-01 | Prerequisite — without this, any ApplicationSetting query stack-overflows once records exist. BUG-03 depends on `ApplicationSetting::all()` working. |
| 2 | BUG-02 | Independent — fixes Arabic hero visibility immediately. |
| 3 | BUG-03 + BUG-04 | One PR: ViewServiceProvider, footer blade, both filament lang files. |
| 4 | BUG-05 | Trivial translation fix, no dependencies. |
