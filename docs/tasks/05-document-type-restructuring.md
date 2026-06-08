# Phase 5: Document Type Restructuring

## Title

Rewrite Document Type Models with Embedded Approval Fields

## Description

Update `CertifiedCenterDocumentType` and `TrainerDocumentType` models to hold `key`, `name` (json), `status`, `admin_notes`, and `reviewed_by_admin_id` directly — removing the old `document_type_id` FK and `is_published` toggle. These tables are now independent from `board_document_types`.

## Vision

Three completely independent document type tables with zero cross-references. Each provider (board, trainer, center) manages its own types. Approval workflow is embedded in each table via the `status`/`admin_notes`/`reviewed_by_admin_id` columns.

## Tips

- The `CertifiedCenterDocumentType` model is used by `CertifiedCenter` via `documentTypes()` and `approvedDocumentTypes()` relations — ensure those still work.
- The `TrainerDocumentType` model is used by `Trainer` via `documentTypes()` relation.
- `DocumentTypeRequestStatus` enum (`Pending`/`Approved`/`Rejected`) is reused for the `status` cast.
- The `certifiedCenter` and `trainer` BelongsTo relations stay but the pivot relation to `DocumentType` is removed.
- Blade views that accessed `$pivot->documentType->name` must now read `$pivot->name` directly.
- `is_published` anywhere in the codebase referring to these models must become `status === 'approved'` or `status === DocumentTypeRequestStatus::Approved`.

## Steps

### Step 1: Rewrite `app/Models/CertifiedCenterDocumentType.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentTypeRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertifiedCenterDocumentType extends Model
{
    use HasFactory;

    protected $table = 'certified_center_document_types';

    protected $fillable = [
        'certified_center_id',
        'key',
        'name',
        'status',
        'admin_notes',
        'reviewed_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'status' => DocumentTypeRequestStatus::class,
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }
}
```

### Step 2: Rewrite `app/Models/TrainerDocumentType.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentTypeRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerDocumentType extends Model
{
    use HasFactory;

    protected $table = 'trainer_document_types';

    protected $fillable = [
        'trainer_id',
        'key',
        'name',
        'status',
        'admin_notes',
        'reviewed_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'status' => DocumentTypeRequestStatus::class,
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }
}
```

### Step 3: Update Blade View

File: `resources/views/web/centers/_details.blade.php`

Replace lines ~67-76:

```blade
@if ($center->approvedDocumentTypes->isNotEmpty())
    <h5 class="mt-4 mb-3">{{ __('web.labels.document_types') }}</h5>
    <div class="d-flex flex-wrap gap-2">
        @foreach ($center->approvedDocumentTypes as $docType)
            <span class="badge bg-primary px-3 py-2">{{ $docType->name }}</span>
        @endforeach
    </div>
@endif
```

### Step 4: Remove `is_published` from language files (optional)

The `is_published` translation keys in `lang/en/app.php` and `lang/ar/app.php` may still be used by `BlogPost` — leave those entries. Only remove if they were specifically for the document type tables.

## Acceptance Criteria

- [ ] `CertifiedCenterDocumentType` model has `key`, `name`, `status`, `admin_notes`, `reviewed_by_admin_id` in `$fillable`
- [ ] `TrainerDocumentType` model has same fields
- [ ] Neither model has `document_type_id` or `is_published`
- [ ] Neither model has a `documentType()` BelongsTo relation
- [ ] Both models have `reviewer()` BelongsTo relation
- [ ] `status` is cast to `DocumentTypeRequestStatus::class`
- [ ] `name` is cast to `array`
- [ ] `_details.blade.php` reads `$docType->name` directly (not through a pivot relation)
- [ ] `approvedDocumentTypes()` on `CertifiedCenter` filters by `status === 'approved'`
