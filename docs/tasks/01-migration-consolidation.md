# Phase 1: Migration Consolidation

## Title

Create Consolidated `create_` Migration Files

## Description

Replace multiple incremental migration files with a single `create_` file per table. Each consolidated file represents the **final schema** after all alterations — no data migrations, no intermediate columns that were later dropped. Since we're doing a fresh install (`migrate:fresh`), there is no legacy data to transform.

## Vision

After this phase, every table has exactly one migration file that creates it. Running `migrate:fresh` creates all tables in the correct order with their final schemas, producing zero errors.

## Tips

- Timestamps determine execution order. `migrate:fresh` sorts by filename. Tables with foreign keys must come **after** the tables they reference.
- Use `json` column type for translatable fields (`name`, `nationality`) — Spatie `HasTranslations` stores JSON.
- `code` and `code_2` on `countries` are nullable but still `unique()` — MySQL allows multiple NULL values in unique columns.
- Do **not** put `unique()` on `countries.name` — it was removed during the JSON conversion and would break with JSON type.
- `accreditation_number` on `certified_centers` is `nullable(false)->unique()` — no data-population script needed for fresh DB.
- `document_code` on `certifications` is `nullable(false)` (non-nullable from the start in consolidated).
- The `certifications` table FK `document_type_id` points to **`board_document_types`** (not `document_types`).
- `is_published` is **not** included anywhere — replaced by `status` in Phase 5 migrations.

## Steps

### Step 1: Create `2025_09_09_113320_create_static_pages_table.php`

Rewrite the same content as the original. No changes needed.

### Step 2: Create `2025_09_09_113321_create_application_settings_table.php`

Consolidate the original creation with `change_value_column_to_text` so `value` is `text` from the start:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_settings');
    }
};
```

### Step 3: Create `2025_09_09_113322_create_countries_table.php`

Final schema:

```php
Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->json('name');
    $table->string('code', 3)->nullable()->unique();
    $table->string('code_2', 2)->nullable()->unique();
    $table->json('nationality')->nullable();
    $table->boolean('is_active')->default(true)->index();
    $table->timestamps();
});
```

### Step 4: Create `2025_09_09_113323_create_board_document_types_table.php`

New table (replaces old `document_types`):

```php
Schema::create('board_document_types', function (Blueprint $table) {
    $table->id();
    $table->string('key', 255)->unique();
    $table->json('name');
    $table->timestamps();
});
```

### Step 5: Create `2025_09_09_113324_create_certified_centers_table.php`

Consolidated from original `create` + `add_country_id` + `make_accreditation_number_required`:

```php
Schema::create('certified_centers', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->string('email', 255)->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password', 255);
    $table->text('address')->nullable();
    $table->string('phone', 255)->nullable();
    $table->string('manager_name', 255)->nullable();
    $table->string('logo')->nullable();
    $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->index();
    $table->string('accreditation_number', 255)->unique();
    $table->dateTime('accreditation_period_start')->nullable();
    $table->dateTime('accreditation_period_end')->nullable();
    $table->string('status')->nullable();
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
});
```

### Step 6: Create `2025_09_09_113325_create_trainers_table.php`

Same as original (already 1 file). No changes.

### Step 7: Create `2025_09_09_113326_create_trainees_table.php`

Consolidated from original `create` + `add_extended_fields`:

```php
Schema::create('trainees', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255)->unique();
    $table->string('email')->nullable()->unique();
    $table->string('phone')->nullable();
    $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
    $table->date('date_of_birth')->nullable();
    $table->string('nationality')->nullable();
    $table->enum('gender', ['male', 'female', 'other'])->nullable();
    $table->string('occupation')->nullable();
    $table->string('organization')->nullable();
    $table->text('address')->nullable();
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone')->nullable();
    $table->text('medical_info')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Step 8: Create `2025_09_09_113327_create_certifications_table.php`

**No `nationality` column.** `document_code` NOT NULL. FK → `board_document_types`. Added `source` enum (`board`/`center`/`trainer`) to track certification origin:

```php
Schema::create('certifications', function (Blueprint $table) {
    $table->id();
    $table->string('source')->default('board');
    $table->foreignId('certified_center_id')->nullable()->constrained('certified_centers')->nullOnDelete();
    $table->string('accredited_serial_number', 100)->index();
    $table->string('document_code', 50);
    $table->string('accreditation_number', 100)->nullable();
    $table->foreignId('document_type_id')->nullable()->constrained('board_document_types')->nullOnDelete();
    $table->date('accreditation_date')->nullable()->index();
    $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
    $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
    $table->foreignId('trainee_id')->nullable()->constrained('trainees')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->string('paper_received', 10)->nullable();
    $table->timestamps();
    $table->unique(['accredited_serial_number', 'document_code'], 'certifications_serial_doc_unique');
});
```

### Step 9: Create `2025_09_09_113328_create_center_accreditation_requests_table.php`

Renamed from `accreditation_requests` to `center_accreditation_requests` to match `trainer_accreditation_requests`. Consolidated original creation with `add_review_fields` and `add_under_review`. Users no longer specify dates — `accreditation_start_date` and `accreditation_end_date` are set by the admin on approval:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_accreditation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->constrained('certified_centers')->cascadeOnDelete();
            $table->text('request_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('accreditation_start_date')->nullable();
            $table->dateTime('accreditation_end_date')->nullable();
            $table->timestamps();
            $table->index(['certified_center_id', 'status'], 'idx_center_active_request_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_accreditation_requests');
    }
};
```

### Step 10: Create `2025_09_09_113329_create_certified_center_document_types_table.php`

New table with embedded approval workflow:

```php
Schema::create('certified_center_document_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('certified_center_id')->constrained('certified_centers')->cascadeOnDelete();
    $table->string('key', 255);
    $table->json('name');
    $table->string('status')->default('pending');
    $table->text('admin_notes')->nullable();
    $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->unique(['certified_center_id', 'key'], 'center_document_unique');
    $table->index('status');
});
```

### Step 11: Create `2025_09_09_113330_create_trainer_document_types_table.php`

New table with embedded approval workflow:

```php
Schema::create('trainer_document_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
    $table->string('key', 255);
    $table->json('name');
    $table->string('status')->default('pending');
    $table->text('admin_notes')->nullable();
    $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->unique(['trainer_id', 'key']);
    $table->index('status');
});
```

### Step 12: Create `2025_09_09_113331_create_trainer_accreditation_requests_table.php`

Consolidated from the existing single migration. Same redesign as center_accreditation_requests: no user-specified dates, `accreditation_start_date`/`accreditation_end_date` set by admin on approval:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_accreditation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->text('request_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('accreditation_start_date')->nullable();
            $table->dateTime('accreditation_end_date')->nullable();
            $table->timestamps();
            $table->index(['trainer_id', 'status'], 'idx_trainer_active_request_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_accreditation_requests');
    }
};
```

### Step 13: Keep existing single-file migrations

These already have 1 file per table — keep as-is:
- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2025_09_09_113320_create_static_pages_table.php`
- `2025_09_09_113325_create_trainers_table.php` (original, unchanged)
- `2025_12_21_114632_create_center_type_requests_table.php`
- `2026_04_11_210322_create_certified_center_payment_agent_persons_table.php`
- `2026_04_11_210324_create_certified_center_financial_requests_table.php`
- `2026_04_11_210327_create_trainer_financial_requests_table.php`
- `2026_04_28_000000_create_blog_posts_table.php`
- `2026_05_30_153628_create_memberships_table.php`
- `2026_05_30_153629_create_contact_us_messages_table.php`

## Acceptance Criteria

- [ ] 12 new consolidated migration files exist in `database/migrations/`
- [ ] Old files listed in Phase 2 still exist (they will be deleted in Phase 2)
- [ ] Migration execution order resolves all FK dependencies correctly
- [ ] No `nationality` column in `certifications` migration
- [ ] `certifications.document_type_id` FK targets `board_document_types`
- [ ] `certifications` migration has `source` column (string, default `'board'`)
- [ ] `is_published` does not appear in any migration schema
- [ ] Accreditation request migrations have `accreditation_start_date`/`accreditation_end_date` (not `requested_start_date`/`requested_end_date`)
