# Phase 3: Model Updates

## Title

Update Models for New Schema and Table Names

## Description

Update Eloquent models to match the consolidated schemas. Key changes: `DocumentType` maps to `board_document_types`, `CertifiedCenterDocumentType` and `TrainerDocumentType` get their initial update (full rewrite in Phase 5), `CertifiedCenter` and `Trainer` replace `documentTypeRequests()` with `documentTypes()`.

## Vision

Every model reflects the database schema exactly. Relations point to correct tables. No model references a deleted class.

## Tips

- `DocumentType` still uses `HasTranslations` with `$translatable = ['name']` — this works with `json` column type.
- The `approvedCenters()` relation on `DocumentType` is removed because the pivot table `certified_center_document_type` no longer exists.
- `CertifiedCenterDocumentType` and `TrainerDocumentType` are **not** deleted — their `$fillable` and relations are rewritten in Phase 5. For now, just update the table name if needed.
- Remove all `use` imports for deleted models (`CenterDocumentTypeRequest`, `TrainerDocumentTypeRequest`).

## Steps

### Step 1: Update `app/Models/DocumentType.php`

```php
class DocumentType extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'board_document_types';   // ADD THIS

    public $translatable = ['name'];

    protected $fillable = ['key', 'name'];

    // REMOVE: public function approvedCenters(): HasMany { ... }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }
}
```

### Step 2: Update `app/Models/CertifiedCenter.php`

Replace the `documentTypeRequests()` relationship with `documentTypes()`:

```php
// REMOVE:
// use App\Models\CenterDocumentTypeRequest;
// public function documentTypeRequests(): HasMany { return $this->hasMany(CenterDocumentTypeRequest::class); }

// ADD:
public function documentTypes(): HasMany
{
    return $this->hasMany(CertifiedCenterDocumentType::class);
}
```

Update the `approvedDocumentTypes()` method (if it exists as a separate method) to filter by status:

```php
public function approvedDocumentTypes(): HasMany
{
    return $this->documentTypes()->where('status', 'approved');
}
```

### Step 3: Update `app/Models/Trainer.php`

Replace `documentTypeRequests()` with `documentTypes()`:

```php
// REMOVE:
// use App\Models\TrainerDocumentTypeRequest;
// public function documentTypeRequests(): HasMany { return $this->hasMany(TrainerDocumentTypeRequest::class); }

// ADD:
public function documentTypes(): HasMany
{
    return $this->hasMany(TrainerDocumentType::class);
}
```

### Step 4: Rename `AccreditationRequest` to `CenterAccreditationRequest`

The table `accreditation_requests` is renamed to `center_accreditation_requests` in Phase 1. The model must be renamed to match.

**File: `app/Models/AccreditationRequest.php`** → rename to `app/Models/CenterAccreditationRequest.php`

Update the class name, fillable, casts, and relation references:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Policies\CenterAccreditationRequestPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(CenterAccreditationRequestPolicy::class)]
class CenterAccreditationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'certified_center_id',
        'request_notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'accreditation_start_date',
        'accreditation_end_date',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'accreditation_start_date' => 'datetime',
            'accreditation_end_date' => 'datetime',
            'status' => AccreditationStatus::class,
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Pending);
    }

    public function scopeUnderReview(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::UnderReview);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Rejected);
    }

    public function scopeForCenter(Builder $query, int $centerId): Builder
    {
        return $query->where('certified_center_id', $centerId);
    }
}
```

Also update the `#[UsePolicy]` attribute to reference `CenterAccreditationRequestPolicy::class` (created in Phase 2 as a rename of `AccreditationRequestPolicy`).

**File: `app/Models/CertifiedCenter.php`**

Update relation and import, and update scope methods to use `accreditation_*` columns:

```php
use App\Models\CenterAccreditationRequest;

public function accreditationRequests(): HasMany
{
    return $this->hasMany(CenterAccreditationRequest::class);
}

// Update hasActiveAccreditationRequest() — was querying requested_start_date/requested_end_date
public function hasActiveAccreditationRequest(): bool
{
    $now = Carbon::now();

    return $this->accreditationRequests()
        ->where('status', AccreditationStatus::Approved)
        ->where('accreditation_start_date', '<=', $now)
        ->where('accreditation_end_date', '>=', $now)
        ->exists();
}

// Update hasApprovedNonExpiredRequest() — was querying requested_end_date
public function hasApprovedNonExpiredRequest(): bool
{
    return $this->accreditationRequests()
        ->where('status', AccreditationStatus::Approved)
        ->where('accreditation_end_date', '>=', now())
        ->exists();
}
```

**File: `app/Models/Trainer.php`**

Update scope methods to use `accreditation_*` columns (same pattern as CertifiedCenter):

```php
public function isAccredited(): bool
{
    if (!$this->is_active) {
        return false;
    }

    return $this->accreditationRequests()
        ->where('status', AccreditationStatus::Approved)
        ->where('accreditation_end_date', '>=', now())
        ->exists();
}

public function hasActiveAccreditationRequest(): bool
{
    return $this->accreditationRequests()
        ->where('status', AccreditationStatus::Approved)
        ->where('accreditation_start_date', '<=', now())
        ->where('accreditation_end_date', '>=', now())
        ->exists();
}
```

**File: `app/Observers/AccreditationRequestObserver.php`** → rename to `app/Observers/CenterAccreditationRequestObserver.php`

Update class name, all type hints, and model references from `AccreditationRequest` to `CenterAccreditationRequest`.

**File: `app/Providers/ObserverServiceProvider.php`**

```php
use App\Models\CenterAccreditationRequest;
use App\Observers\CenterAccreditationRequestObserver;

CenterAccreditationRequest::observe(CenterAccreditationRequestObserver::class);
```

**Files to update references** (table name `accreditation_requests` → `CenterAccreditationRequest` class or `center_accreditation_requests` table):
- `app/Policies/AccreditationRequestPolicy.php` → rename to `CenterAccreditationRequestPolicy`, update class name
- All Filament resources under `app/Filament/Admin/Resources/AccreditationRequests/` → rename namespaces and model references (handled in Phase 6)

### Step 5: Update `TrainerAccreditationRequest` model

**File: `app/Models/TrainerAccreditationRequest.php`**

Replace `requested_start_date`/`requested_end_date` with `accreditation_start_date`/`accreditation_end_date`:

```php
protected $fillable = [
    'trainer_id',
    'request_notes',
    'status',
    'admin_notes',
    'reviewed_by',
    'reviewed_at',
    'accreditation_start_date',
    'accreditation_end_date',
];

protected function casts(): array
{
    return [
        'accreditation_start_date' => 'datetime',
        'accreditation_end_date' => 'datetime',
        'status' => AccreditationStatus::class,
        'reviewed_at' => 'datetime',
    ];
}
```

### Step 6: Create `CertificationSource` enum

**File: `app/Enums/CertificationSource.php`**

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum CertificationSource: string
{
    case Board = 'board';
    case Center = 'center';
    case Trainer = 'trainer';
}
```

### Step 7: Update `Certification` model

**File: `app/Models/Certification.php`**

Add `'source'` to `$fillable`, remove `'nationality'` (handled in Phase 4), add `CertificationSource` cast:

```php
protected $fillable = [
    'certified_center_id',
    'trainee_id',
    'source',               // ADD
    'accredited_serial_number',
    'document_code',
    'accreditation_number',
    'document_type_id',
    'accreditation_date',
    'trainer_id',
    'country_id',
    'paper_received',
    'notes',
];

protected function casts(): array
{
    return [
        'accreditation_date' => 'date',
        'source' => CertificationSource::class,  // ADD
    ];
}
```

Update the `byNationality` scope to use the country relationship instead (was querying the removed `nationality` column):

```php
#[Scope]
protected function byNationality(Builder $query, string $nationality): void
{
    $query->whereHas('country', function (Builder $q) use ($nationality) {
        $q->where('nationality', $nationality);
    });
}
```

## Acceptance Criteria

- [ ] `DocumentType` model has `protected $table = 'board_document_types'`
- [ ] `approvedCenters()` relation removed from `DocumentType`
- [ ] `certifications()` relation preserved on `DocumentType`
- [ ] No `use` statements reference `CenterDocumentTypeRequest` or `TrainerDocumentTypeRequest`
- [ ] `CertifiedCenter` has `documentTypes()` relation (not `documentTypeRequests()`)
- [ ] `Trainer` has `documentTypes()` relation (not `documentTypeRequests()`)
- [ ] All models use proper `HasMany`/`BelongsTo` imports from `Illuminate\Database\Eloquent\Relations`
- [ ] `AccreditationRequest` model renamed to `CenterAccreditationRequest` with `$table = 'center_accreditation_requests'`
- [ ] `CertifiedCenter::accreditationRequests()` returns `HasMany<CenterAccreditationRequest>`
- [ ] `ObserverServiceProvider` registers `CenterAccreditationRequestObserver` for `CenterAccreditationRequest`
- [ ] `AccreditationRequestObserver` renamed to `CenterAccreditationRequestObserver`, all type hints updated
- [ ] `CenterAccreditationRequest` fillable uses `accreditation_start_date`/`accreditation_end_date` (not `requested_*`)
- [ ] `TrainerAccreditationRequest` fillable uses `accreditation_start_date`/`accreditation_end_date` (not `requested_*`)
- [ ] `CertifiedCenter::hasActiveAccreditationRequest()` queries `accreditation_start_date`/`accreditation_end_date`
- [ ] `CertifiedCenter::hasApprovedNonExpiredRequest()` queries `accreditation_end_date`
- [ ] `Trainer::isAccredited()` and `Trainer::hasActiveAccreditationRequest()` use `accreditation_*` columns
- [ ] `CertificationSource` enum created with `board`/`center`/`trainer` cases
- [ ] `Certification` model has `'source'` in `$fillable` and `CertificationSource` cast
- [ ] `Certification::byNationality()` scope uses `whereHas('country', ...)` instead of `where('nationality', ...)`
