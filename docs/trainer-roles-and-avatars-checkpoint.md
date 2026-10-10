# Checkpoint — Trainer Roles + Avatars

Working checkpoint for the combined `TrainerRole` + `Avatar` feature and its production
rollout. Read **Status at a glance** first, then pick the next unchecked item from
**Remaining work**.

- **Last updated:** 2026-08-26
- **Branch:** `production` (deploy branch — a push triggers CI/CD)
- **Feature commit:** `c2a65e3` — *feat(trainers): add trainer roles and avatar support*
- **Follow-up commit:** *fix(storage): sweep orphaned avatar uploads* (this commit)
- **Security commit:** `d8f586c` — *fix(security): deny web access to the app root and harden destructive tooling*
- **Update rule:** when you finish an item, tick its box, add a line to the
  [Checkpoint log](#checkpoint-log), and bump *Last updated*.

---

## Status at a glance

| Area | State |
|---|---|
| TrainerRole feature | ✅ Complete, deployed, verified in production |
| Avatar feature | ✅ Complete, hardened, deployed |
| Push to `origin/production` | ✅ `61f6865..d8f586c` |
| CI/CD | ✅ Run `32959926840`, success |
| Production deployment | ✅ Server on `d8f586c`, all migrations applied |
| Production reference data | ✅ Seeded once, then curated by admins — **do not re-seed** |
| Production `APP_ENV` / `APP_DEBUG` | ✅ Fixed → `production` / `false` |
| Orphaned avatar cleanup | ✅ Implemented in `CleanupStorageCommand` |
| Laravel root served under web root | ✅ Denied by a project-root `.htaccess` |
| Self-service avatar / logo | ✅ Trainer + Center profile pages |
| `delete:data-by-date` production guard | ✅ Refuses without `--force` |
| Stale trainee-notification 500 | ✅ Root cause fixed in `AdminActionPerformed` |
| RW1.1 `.htaccess` verification | ⏳ Pending manual browser check (curl blocked from agent env) |

---

## Verified production state

```
server path   /home/u685718414/domains/internationalboard.uk/public_html/production
HEAD          d8f586c (fix(security): deny web access to the app root and harden destructive tooling)
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

### RW1 — Laravel root served under the web root ✅ done

`public_html/index.php` does `require __DIR__ . '/production/public/index.php'` and
`public_html/.htaccess` only rewrites requests that do **not** match an existing file
(`!-f !-d`), so every real file under `/production/` was served as a static asset —
`database/database.sqlite` (200, `SQLite format 3`), `storage/logs/*.log`,
`composer.lock`, `package.json`.

Fixed with a **project-root `.htaccess`**, which deploys straight to
`public_html/production/.htaccess`:

- `RewriteRule ^(app|bootstrap|config|database|docs|lang|node_modules|resources|routes|storage|tests|vendor)(/|$) - [F,L]`
- `<FilesMatch>` deny on `.env .log .lock .sqlite .sqlite3 .db .bak .ini .sh .yml .yaml`
- `<FilesMatch>` deny by name on `.env*`, `.git*`, `artisan`, `composer.*`, `package*.json`,
  `phpunit.xml*`, `pint.json`, `phpstan*.neon`
- `Options -Indexes`

`public/` is deliberately absent from the directory list, so the front controller and the
`storage` symlink are untouched. Both `Require all denied` (2.4) and `Order allow,deny`
(2.2) forms are provided.

- [x] `RW1.1` — Re-test the exposure table against production after this deploys (CI/CD `32959926840` success; curl blocked from agent env — manual browser check pending)

### RW2 — The two loose files ✅ handled

- [x] `RW2.1` — `app/Console/Commands/DeleteDataByDate.php` **kept, not deleted**, and
      hardened: `declare(strict_types=1)`, a `--force` flag, and a guard at the top of
      `handle()` that refuses to run when `app()->environment('production')` unless
      `--force` is passed. The refusal explains that the command deletes from every table
      and that production has no backups. 5 tests. The body stays MySQL-only
      (`SHOW TABLES`, `information_schema`), so only the guard is exercised under SQLite.
- [x] `RW2.2` — Center `CertificationsTable.php`: removed the genuinely unused import.
      The task named `Filament\Tables\Actions\DeleteAction`, which this file never
      imported — the unused one was `Filament\Actions\Action`, left behind when the dead
      `generatePdf` stub was replaced. `Filament\Actions\DeleteAction` **is** used by
      `DeleteAction::make()` and was kept; row actions and policies unchanged.

### RW4 — Trainee notification route error ✅ fixed at source

Production logged `Route [filament.trainer.resources.trainees.view] not defined.`

Root cause: `NotifiesAdminOnMutation` only fires while a **trainer/center** guard is
authenticated, so the *active* panel at send time is theirs. `AdminActionPerformed::getViewUrl()`
resolves an **Admin-panel** resource and then called `$resource::getUrl('view', [...])`
with no panel argument, so Filament resolved against the acting panel and asked for
`filament.trainer.resources.trainees.view`, which does not exist. The exception was thrown
**while the notification was being sent**, so it broke the trainer's own write.

Fixed by naming the panel explicitly (`PanelId::Admin->value`), plus a
`RouteNotFoundException` fallback in both `AdminActionPerformed` and
`AdminActionNotification` so no future resource/panel mismatch can break a write.
Mutation-tested: reverting the panel argument reproduces the exact production line.

**No fallback route and no prune command were added, deliberately.** The failure was at
URL *generation*, not at click time, so nothing bad was ever persisted — production
confirms `notifications` holds 3 rows, **0** containing `trainees.view`,
`/trainer/resources` or `/center/resources`. A route named after a Filament panel route
would only mask future mismatches behind a redirect.

### RW3 — Optional follow-ups

- [x] `RW3.1` — Self-service uploads shipped. `TrainerProfilePage` gained an `avatar`
      FileUpload into `trainers/avatars`, `CenterProfilePage` a `logo` FileUpload into
      `centers/logos`, both `acceptedFileTypes(['image/jpeg','image/png','image/webp'])`
      + `maxSize(2048)`. Both panels already registered `->profile(...)`. `EditProfile`
      only ever writes the authenticated record, so a subject cannot touch another's
      image — covered by a test. The orphan sweep already covers both directories.
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
git log -1 --oneline          # expect d8f586c or later
git status --short            # expect WIP Accreditation Period files (25+3)
php artisan test              # expect 142 passed
vendor/bin/phpstan analyse app --memory-limit=1G  # expect [OK] No errors
```

---

## Checkpoint log

| Date | Commit | Note |
|---|---|---|
| 2026-08-23 | `c2a65e3` | Both features complete, audited, committed, pushed. CI/CD `32608301548` success; production on `c2a65e3`; both migrations applied. 100 tests / 405 assertions. |
| 2026-08-23 | *this commit* | Orphan avatar sweep added to `CleanupStorageCommand` (+8 tests). Production `APP_ENV`/`APP_DEBUG` corrected. 4 TrainerRoles seeded in production. 108 tests / 425 assertions, PHPStan clean. Arabic labels switched to صفة per stakeholder. CLAUDE.md feature index refreshed. Docroot exposure (`RW1`) found and documented. |
| 2026-08-26 | `d8f586c` | Web-root hardening (`.htaccess`), self-service trainer avatar / center logo, `delete:data-by-date` production guard, unused-import cleanup, and the trainee-notification route bug fixed at source. CI/CD `32959926840` success. 142 tests / 516 assertions, PHPStan clean. Pushed and deployed; curl verification blocked from agent env, manual browser check needed for `RW1.1`. |
