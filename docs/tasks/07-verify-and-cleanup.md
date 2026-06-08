# Phase 7: Verify & Cleanup

## Title

Run `migrate:fresh`, Seed, Clear Cache, and Verify

## Description

Execute the final validation: run `migrate:fresh` to prove all migrations work together, run seeders to populate reference data, clear all caches, and verify no broken routes or missing classes.

## Vision

A clean, working database with all tables created in the correct order. Zero migration errors. Zero route errors. Zero missing class errors. The project is ready for development.

## Tips

- Run `composer dump-autoload` after deleting model files to regenerate the autoloader.
- The route cache file `bootstrap/cache/routes-v7.php` references deleted Filament classes — it must be deleted or regenerated.
- Run `php artisan route:clear` (not `route:cache`) since cache references old classes.
- If `migrate:fresh` fails, the error will likely be a FK ordering issue — check timestamps in the migration filenames.
- If `migrate:fresh` succeeds but `db:seed` fails, check that seeders don't reference deleted models.
- Check the `DocumentTypeSeeder` (if it exists) to ensure it seeds `board_document_types` (not the old `document_types`).
- Check `config/auth.php` and any service providers for references to old model/table names — unlikely to need changes but worth a quick scan.

## Steps

### Step 1: Regenerate autoloader

```bash
composer dump-autoload
```

### Step 2: Clear route cache

```bash
php artisan route:clear
```

Also delete the cached route file if it still exists:

```bash
rm -f bootstrap/cache/routes-v7.php
```

### Step 3: Run migrate:fresh

```bash
php artisan migrate:fresh
```

Expected output: each migration runs in order, all tables created.

**If it fails**, check:
- FK dependency order (e.g., `certified_centers` must come after `countries`)
- Table name mismatches (e.g., `board_document_types` vs `document_types`)
- Duplicate table names

### Step 4: Run seeders

```bash
php artisan db:seed
```

Expected output: reference data populates (countries, board_document_types, etc.)

**If it fails**, check:
- `CountrySeeder` uses correct columns (`json` for name/nationality)
- `DocumentTypeSeeder` inserts into `board_document_types` (not the old `document_types` table)
- No seeder references deleted models (`CenterDocumentTypeRequest`, `TrainerDocumentTypeRequest`)
- No seeder inserts into old table names (`document_types`, `center_document_type_requests`, `trainer_document_type_requests`)

### Step 5: Verify route registration

```bash
php artisan route:list
```

Expected: no errors. All routes compile.

**If it fails**, check:
- Filament resources reference existing models
- No files reference deleted classes (`CenterDocumentTypeRequest`, `TrainerDocumentTypeRequest`)
- The cached route file was deleted

### Step 6: Verify each panel loads

Quick sanity check — run a simple PHP check:

```bash
php artisan tinker --execute="echo 'App\Models\DocumentType::class loaded: ' . (class_exists('App\Models\DocumentType') ? 'OK' : 'FAIL');"
php artisan tinker --execute="echo 'App\Models\CertifiedCenterDocumentType::class loaded: ' . (class_exists('App\Models\CertifiedCenterDocumentType') ? 'OK' : 'FAIL');"
php artisan tinker --execute="echo 'App\Enums\CertificationSource::class loaded: ' . (class_exists('App\Enums\CertificationSource') ? 'OK' : 'FAIL');"
```

Verify deleted models raise errors:

```bash
php artisan tinker --execute="echo class_exists('App\Models\CenterDocumentTypeRequest') ? 'STILL EXISTS' : 'CORRECTLY DELETED';"
php artisan tinker --execute="echo class_exists('App\Models\TrainerDocumentTypeRequest') ? 'STILL EXISTS' : 'CORRECTLY DELETED';"
```

### Step 7: Check final file count

```bash
ls database/migrations/*.php | wc -l
```

Expected: **24** migration files (12 consolidated + 12 kept as-is).

### Step 8: Final grep scan for stale references

Run these grep commands from the project root. Each should return no results (or only expected results for unaffected models like Trainee/Country/BlogPost).

```bash
# 1. No nationality on Certification (Trainee/Country/Trainer have their own nationality — those are fine)
grep -rn "->nationality" app/ --include="*.php" | grep -v "Trainee" | grep -v "Country" | grep -v "Trainer"

# 2. No document_type_id on center/trainer doc type models
grep -rn "document_type_id" app/Models/CertifiedCenterDocumentType.php app/Models/TrainerDocumentType.php

# 3. No is_published on doc type models (BlogPost is fine)
grep -rn "is_published" app/Models/CertifiedCenterDocumentType.php app/Models/TrainerDocumentType.php

# 4. No documentType() on center/trainer doc type models
grep -rn "function documentType" app/Models/CertifiedCenterDocumentType.php app/Models/TrainerDocumentType.php

# 5. No bare accreditation_requests (should be center_accreditation_requests or trainer_accreditation_requests)
grep -rn "accreditation_requests" app/ --include="*.php" | grep -v "center_accreditation_requests" | grep -v "trainer_accreditation_requests"

# 6. No references to deleted request models in app/ code
grep -rn "CenterDocumentTypeRequest" app/ --include="*.php"
grep -rn "TrainerDocumentTypeRequest" app/ --include="*.php"

# 7. No references to old request table names in app/ code
grep -rn "center_document_type_requests" app/ --include="*.php"
grep -rn "trainer_document_type_requests" app/ --include="*.php"

# 8. No documentTypeRequests() method calls anywhere in app/
grep -rn "documentTypeRequests" app/ --include="*.php"

# 9. No 'document_types' as a raw table string (excluding board_document_types)
grep -rn "'document_types'" app/ --include="*.php"
grep -rn '"document_types"' app/ --include="*.php"
```

> **Note:** Pattern 1 may legitimately match `Trainee`, `Country`, and `Trainer` models (they have their own `nationality` column). Pattern 3 may match `BlogPost` (it still uses `is_published`). All other patterns should return zero results.

### Step 8a: Action guide for each grep hit

If any grep pattern in Step 8 returns results, use this table to fix them:

| Found pattern | Replace with / Action |
|---|---|
| `Certification::nationality` or `->nationality` on Certification model | Use `$certification->country?->nationality` instead |
| `'document_types'` used as raw table name in queries/views | Change to `'board_document_types'` |
| `is_published` on `CertifiedCenterDocumentType` / `TrainerDocumentType` | Replace with `status === 'approved'` or scope `approved()` |
| `documentTypeRequests()` on `CertifiedCenter` / `Trainer` | Replace with `documentTypes()` (points to `CertifiedCenterDocumentType` / `TrainerDocumentType`) |
| `CenterDocumentTypeRequest` or `TrainerDocumentTypeRequest` class | Remove reference entirely. Replace with corresponding `CertifiedCenterDocumentType` / `TrainerDocumentType` |
| `center_document_type_requests` or `trainer_document_type_requests` table name | Replace with `certified_center_document_types` / `trainer_document_types` |
| `document_type_id` on `CertifiedCenterDocumentType` / `TrainerDocumentType` | Remove column — these models store `key` and `name` directly |
| Logic that creates a request (old flow) | Insert into `certified_center_document_types` / `trainer_document_types` with `status = 'pending'` |

### Step 9: Manual workflow validation

Run a quick end-to-end test to confirm the new document type workflow works:

```bash
php artisan tinker
```

```php
# Create a board document type (admn)
$boardDoc = \App\Models\DocumentType::create(['key' => 'test_board', 'name' => ['en' => 'Test Board Doc', 'ar' => 'اختبار']]);
echo "Board doc type created: " . $boardDoc->id . PHP_EOL;

# Create a center document type (pending)
$center = \App\Models\CertifiedCenter::first();
$centerDoc = \App\Models\CertifiedCenterDocumentType::create([
    'certified_center_id' => $center->id,
    'key' => 'test_center_doc',
    'name' => ['en' => 'Test Center Doc'],
    'status' => 'pending',
]);
echo "Center doc type created: " . $centerDoc->id . ", status: " . $centerDoc->status . PHP_EOL;

# Approve it
$admin = \App\Models\User::first();
$centerDoc->update([
    'status' => 'approved',
    'admin_notes' => null,
    'reviewed_by_admin_id' => $admin->id,
]);
echo "Center doc type approved: " . $centerDoc->fresh()->status . PHP_EOL;

# Verify certifiedCenter relation
echo "Center name: " . $centerDoc->certifiedCenter->name . PHP_EOL;

# Verify reviewer relation
echo "Reviewed by: " . $centerDoc->reviewer->name . PHP_EOL;

# Verify the approvedDocumentTypes scope on CertifiedCenter
echo "Approved doc types for center: " . $center->approvedDocumentTypes()->count() . PHP_EOL;
```

Expected: all operations succeed. No SQL errors. Both sides of the BelongsTo relations resolve correctly.

## Acceptance Criteria

- [ ] `composer dump-autoload` completes without errors
- [ ] `php artisan route:clear` succeeds
- [ ] `php artisan migrate:fresh` creates all tables successfully
- [ ] `php artisan db:seed` completes without errors
- [ ] `php artisan route:list` shows all routes without errors
- [ ] Deleted model classes raise errors when `class_exists()` is called
- [ ] Exactly 27 migration files in `database/migrations/`
- [ ] No stale `->nationality` references on Certification model
- [ ] No stale `document_type_id` or `is_published` on doc type models
- [ ] No references to deleted request models (`CenterDocumentTypeRequest`, `TrainerDocumentTypeRequest`) in `app/`
- [ ] No references to old request table names (`center_document_type_requests`, `trainer_document_type_requests`) in `app/`
- [ ] No `documentTypeRequests()` method calls remain in `app/`
- [ ] No raw `'document_types'` table string references (except `board_document_types`)
- [ ] Manual workflow test in Step 9 completes without errors
