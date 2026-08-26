# Checkpoint — Trainer Roles + Avatars

Working checkpoint for the combined `TrainerRole` + `Avatar` feature and its production
rollout. Read **Status at a glance** first, then pick the next unchecked item from
**Remaining work**.

- **Last updated:** 2026-08-23
- **Branch:** `production` (deploy branch — a push triggers CI/CD)
- **Feature commit:** `c2a65e3` — *feat(trainers): add trainer roles and avatar support*
- **Follow-up commit:** *fix(storage): sweep orphaned avatar uploads* (this commit)
- **Update rule:** when you finish an item, tick its box, add a line to the
  [Checkpoint log](#checkpoint-log), and bump *Last updated*.

---

## Status at a glance

| Area | State |
|---|---|
| TrainerRole feature | ✅ Complete, deployed, verified in production |
| Avatar feature | ✅ Complete, hardened, deployed |
| Push to `origin/production` | ✅ `61f6865..c2a65e3` |
| CI/CD | ✅ Run `32608301548`, success, 43s |
| Production deployment | ✅ Server on `c2a65e3`, both migrations applied |
| Production reference data | ✅ Seeded once, then curated by admins — **do not re-seed** |
| Production `APP_ENV` / `APP_DEBUG` | ✅ Fixed → `production` / `false` |
| Orphaned avatar cleanup | ✅ Implemented in `CleanupStorageCommand` |
| **Laravel root served under web root** | ⛔ **Open — needs a decision** |

---

## Verified production state

```
server path   /home/u685718414/domains/internationalboard.uk/public_html/production
HEAD          c2a65e3a76cac3949a44ce54d0eb02125f149cfb
git status    (clean)
Laravel       13.24.0     PHP 8.5.4     Filament v4.12.6
migrations    2026_08_19_000001_add_avatar_to_users_table ..... [6] Ran
              2026_08_21_000001_create_trainer_roles_table .... [6] Ran   (0 pending)
data (08-26)  trainers=15  certifications=4589  centers=13  users=5
              trainer_roles=1 (admin-created)  trainers_with_role=0
uploads       26 files on disk, 19 referenced, 7 orphans   storage symlink LINKED
config        app.env=production  app.debug=false  isProduction=true
```

Deployment is non-destructive by design: the commit message carries no destructive
keyword, so `check-destructive` resolved `is_destructive=false` and the deploy ran
`php artisan migrate --force` — never `migrate:fresh`.

---

## Done

### TrainerRole

- [x] `trainer_roles` table (`id`, `json('name')`, timestamps); `trainers.trainer_role_id`
      nullable FK with `nullOnDelete()`, matching all 9 existing nullable lookup FKs
- [x] `Trainer belongsTo TrainerRole` / `TrainerRole hasMany Trainer`, no pivot
- [x] Factory cycles a fixed dataset (no overflow at `count(20)`); seeder idempotent
- [x] `TrainerSeeder` assigns roles round-robin, leaves one trainer null
- [x] Admin CRUD resource + `TrainerRolePolicy` on `User::isAdmin()`
- [x] Center can assign a role but cannot administer roles — resource is Admin-namespace
      only; production `route:list` shows no `center/` or `trainer/` trainer-role routes
- [x] Trainer panel deliberately unchanged (no comparable read-only profile surface)
- [x] Public profile badge behind the existing `show_in_public_website` gate
- [x] Eager loading mutation-tested (`LazyLoadingViolationException` without it)
- [x] en/ar localization; 25 tests
- [x] Rollback tested **with a live role assignment**; 12/12 local trainers survived
- [x] Production: 4 roles seeded, `distinct_en=4` (no duplicates), 14 trainers untouched

### Avatar

- [x] `users.avatar` column (nullable, additive, reversible)
- [x] `User`, `Trainer`, `CertifiedCenter` implement Filament `HasAvatar`
- [x] `'avatar'` added to `User` `#[Fillable]` — without it every upload would throw
      under `Model::preventSilentlyDiscardingAttributes()`
- [x] Admin user resource: `FileUpload` + `ImageColumn` + `ImageEntry`
- [x] **All four upload points hardened**: `acceptedFileTypes(['image/jpeg','image/png','image/webp'])`
      + `maxSize(2048)`. Mutation-tested — without it an SVG carrying `<script>` and a
      4 MB image were both accepted
- [x] 21 tests
- [x] **Orphan sweep** added to `CleanupStorageCommand` (already scheduled daily 03:00),
      with a 24 h grace period so an in-flight upload is never deleted. 8 tests; both
      guards mutation-tested

### Production configuration

- [x] `APP_ENV` `local` → `production`, `APP_DEBUG` `true` → `false` in the server `.env`
      (timestamped backup kept beside it), then `optimize:clear` + `config:cache` +
      `route:cache` + `view:cache`, matching the deploy script's own sequence
- [x] Verified Laravel sees the new values; all public pages and all three panel logins
      still return 200; error responses no longer leak stack traces

---

## Remaining work

### RW1 — Laravel root is served under the web root ⛔ **needs a decision**

`public_html/index.php` is `require __DIR__ . '/production/public/index.php';` and
`public_html/.htaccess` only rewrites requests that do **not** match an existing file
(`!-f !-d`). Real files under `/production/` are therefore served directly:

| Path | Result |
|---|---|
| `/production/database/database.sqlite` | **200** — 245 760 bytes, `SQLite format 3` |
| `/production/storage/logs/laravel-2026-08-22.log` | **200** — stack traces, absolute paths |
| `/production/composer.lock` | **200** — full dependency inventory |
| `/production/package.json` | **200** |
| `/production/.env`, `.env.*`, `.git/*`, `.gitignore` | 403 (dotfile rule) |
| `/production/**/*.php` | 500 (executed, not disclosed) |

Severity is bounded: the exposed SQLite file is an **empty leftover** — 21 tables,
0 rows in `users`, `trainers`, `certifications`, `certified_centers`, `trainees` — so this
is schema and log disclosure, not user-data disclosure. It is **pre-existing**
infrastructure (docroot layout, unrelated to `c2a65e3`), and `APP_DEBUG=false` now
limits what future logs contain.

- [ ] `RW1.1` — Decide the fix. Options, least to most invasive:
      1. Point the Hostinger domain docroot at `public_html/production/public` directly
         (correct fix; hosting-panel change)
      2. Add `RewriteRule ^production/(?!public/) - [F,L]` at the top of
         `public_html/.htaccess`
      3. Move `database/database.sqlite` out of the tree (it is unused — production is MySQL)
- [ ] `RW1.2` — Apply and re-test the table above

Not done unattended: options 1 and 2 can take the live site down if the rewrite
interacts badly with LiteSpeed, and option 3 deletes a file.

### RW2 — Two uncommitted local files ⛔ **needs a decision**

- [ ] `RW2.1` — `app/Console/Commands/DeleteDataByDate.php` (untracked). Deletes rows
      from **every** table in a date range; `--disable-fk` issues
      `SET FOREIGN_KEY_CHECKS=0`; with no argument it defaults to **today**. It has
      `--dry-run` and `--exclude`, but **no confirmation prompt and no production guard**,
      and it omits `declare(strict_types=1)` (fails Pint, 7 fixers).
      **Deliberately not committed and never executed.** Needs a guard + confirmation
      before it belongs in the repo.
- [ ] `RW2.2` — `app/Filament/Center/Resources/Certifications/Tables/CertificationsTable.php`
      (modified). Swaps a dead `generatePdf` stub (`->action(function ($record) {})`) for
      `DeleteAction`. **Not a privilege escalation** — `CertificationResource::canDelete()`
      / `canDeleteAny()` already gate on `AccreditationGateService`, `CertificationPolicy::delete()`
      already allows the owning centre, and `DeleteBulkAction` was already in the toolbar;
      this only surfaces an existing capability as a row action. Leaves an unused `Action`
      import (Pint failure). Unrelated to these features, so left uncommitted.

### RW4 — Unrelated pre-existing bug (reported, untouched)

Production logged twice on 2026-08-25:

```
production.ERROR: Route [filament.trainer.resources.trainees.view] not defined.
```

The Trainer panel has no Trainees resource (`Certifications`,
`TrainerAccreditationRequests`, `TrainerDocumentTypes`, `TrainerFinancialRequests` only),
and no code in the repo references that route name — so it comes from a runtime source,
most likely a stored notification whose action URL points at a route that only exists in
another panel. Unrelated to TrainerRole/Avatar (zero `trainerRole|avatar` matches in that
log) and not introduced by either commit.

- [ ] `RW4.1` — Trace the notification/link that builds that URL and repoint or guard it

### RW3 — Optional follow-ups

- [ ] `RW3.1` — Decide whether trainers/centres should manage their own avatar. Today only
      an admin (or the owning centre, for its trainers) can. No panel registers `->profile()`;
      `TrainerProfilePage` / `CenterProfilePage` expose only name/email/password.
- [x] `RW3.2` — CLAUDE.md refresh (done in this commit): add a `TrainerRole` model/resource row, record
      `User avatar → users/avatars/` and the jpeg/png/webp + 2 MB rule, and fix the stale
      claim that `tests/Unit` and `tests/Feature` are empty (10 test files now).
- [ ] `RW3.3` — Re-run `graphify update .` (gitignored, no repo impact).

---

## Deliberately untouched

- **11 pre-existing Pint failures** in already-committed files (`CertificationResource`,
  `FinancialRequestsRelationManager`, `CsvExportHandler`, `CsvHeaderMapper`, 5 `config/*.php`).
  Every file touched by this work passes Pint.
- **`ArchitectureServiceProvider`'s `$table` / `$primaryKey` check is a no-op** — both are
  declared on the base `Model`, so `property_exists()` is always true.
- **No localized-name uniqueness on `TrainerRole`.** No existing localized lookup enforces
  it: `countries`, `board_document_types` and `specializations` all store `json('name')`
  with no unique index, and uniqueness lives on a separate scalar where needed
  (`countries.code`, `board_document_types.key`). Duplicate role names are expected
  behavior, pinned by a test.
- **`agent_persons` is empty in production** — that seeder is demo data (it has a factory),
  unlike the reference seeders.

---

## Seeding: what happened, and why not to repeat it

`TrainerRoleSeeder` was run once on 2026-08-23 because production already carried its
analogues' reference data:

```
countries            34
specializations      12   <- exactly SpecializationSeeder's 12 entries, ids 1-12, in order
board_document_types 391
application_settings 15
memberships           4
static_pages          6
agent_persons         0   <- demo seeder, has a factory
```

`SpecializationSeeder` is the closest analogue — same entity class (admin-managed
localized lookup attached to `Trainer`), same shape (`json('name')`, en/ar), registered
adjacently in `DatabaseSeeder` — and its output is present in production verbatim.
Only `TrainerRoleSeeder` was run, twice, to prove idempotency. No roles were assigned to
existing trainers; `TrainerSeeder` was **not** run.

### ⛔ Do not re-run `TrainerRoleSeeder` on production

Within a day the admins **rejected the defaults**. As of 2026-08-26 production holds a
single role they created themselves:

```
id=5  en="Administrator and Training Expert"  ar="مستشار وخبير تدريب"
      trainers=0  created=2026-08-23 14:54:36     max_id=5
```

All four seeded roles (ids 1-4) were deleted through the admin panel. Re-running the
seeder would resurrect data an administrator deliberately removed. The business manages
this vocabulary itself — treat `trainer_roles` as **admin-owned, not seeded**.

This is also the strongest production proof the feature works end to end: an admin
created a role, deleted four others, and all trainers survived (`nullOnDelete()` held,
15/15 intact).

---

## Resume protocol

```bash
cd /home/lenovo/work/projects/international-board
git log -1 --oneline
git status --short        # expect only the RW2 files
php artisan test          # expect 108 passed
```

---

## Checkpoint log

| Date | Commit | Note |
|---|---|---|
| 2026-08-23 | `c2a65e3` | Both features complete, audited, committed, pushed. CI/CD `32608301548` success; production on `c2a65e3`; both migrations applied. 100 tests / 405 assertions. |
| 2026-08-23 | *this commit* | Orphan avatar sweep added to `CleanupStorageCommand` (+8 tests). Production `APP_ENV`/`APP_DEBUG` corrected. 4 TrainerRoles seeded in production. 108 tests / 425 assertions, PHPStan clean. Arabic labels switched to صفة per stakeholder. CLAUDE.md feature index refreshed. Docroot exposure (`RW1`) found and documented. |
