# Phase 6: Filament Resource Updates

## Title

Update Filament Resources for Document Type Approval Workflow

## Description

Update Filament resources across Admin, Center, and Trainer panels to work with the new document type structure. The old request-flow resources have been deleted (Phase 2). Now update the remaining resources to display `key`/`name` (not `documentType.name`), show `status` badges (not `is_published` toggles), and support the approval workflow for admin users.

## Vision

Each panel shows the correct document type data inline. Admin can approve/reject document type requests by updating `status`, `admin_notes`, and `reviewed_by_admin_id` directly on the record. Centers and Trainers can create new document types (status: pending) and view their status.

## Tips

- `SelectFilter::make('document_type_id')` with `relationship('documentType', ...)` should now use `SelectFilter::make('status')` or be replaced entirely.
- `ToggleColumn::make('is_published')` becomes a `TextColumn::make('status')` with badge colors or an `IconColumn` for approved status.
- In admin panels, the approve/reject action can be a `Action::make('approve')` / `Action::make('reject')` button that updates `status`, `admin_notes`, and `reviewed_by_admin_id`.
- Center/Trainer panels should scope queries to `certified_center_id`/`trainer_id` from the authenticated user.
- The `Admin\DocumentTypes\DocumentTypeResource` (board doc types) largely stays the same — it manages `board_document_types`. Only remove the `ApprovedCentersRelationManager` reference.

## Steps

### Step 1: Update Admin DocumentTypeResource (Board Document Types)

File: `app/Filament/Admin/Resources/DocumentTypes/DocumentTypeResource.php`

Remove `ApprovedCentersRelationManager` from `getRelations()`:

```php
public static function getRelations(): array
{
    return [
        // REMOVED: ApprovedCentersRelationManager::class,
    ];
}
```

### Step 2: Update Admin CertifiedCenterDocumentType Resource

Files to update:
- `app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/Tables/CertifiedCenterDocumentTypesTable.php`
- `app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/Schemas/CertifiedCenterDocumentTypeForm.php`
- `app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/Schemas/CertifiedCenterDocumentTypeInfolist.php`

**Table** — replace `documentType.name` with `name`, replace `is_published` with `status`:

```php
// Column
TextColumn::make('name')
    ->label(__('app.document_type'))
    ->searchable()
    ->sortable()
    ->getStateUsing(fn ($record) => $record->name),

TextColumn::make('status')
    ->label(__('app.status'))
    ->badge()
    ->color(fn (DocumentTypeRequestStatus $state): string => $state->color()),

// Filters
SelectFilter::make('status')
    ->options(DocumentTypeRequestStatus::class),
```

**Actions** — add approve/reject:

```php
Action::make('approve')
    ->label(__('app.approve'))
    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Pending)
    ->action(function ($record) {
        $record->update([
            'status' => DocumentTypeRequestStatus::Approved,
            'reviewed_by_admin_id' => Auth::id(),
        ]);
    }),

Action::make('reject')
    ->label(__('app.reject'))
    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Pending)
    ->form([
        Textarea::make('admin_notes')
            ->label(__('app.rejection_reason'))
            ->required(),
    ])
    ->action(function ($record, array $data) {
        $record->update([
            'status' => DocumentTypeRequestStatus::Rejected,
            'admin_notes' => $data['admin_notes'],
            'reviewed_by_admin_id' => Auth::id(),
        ]);
    }),
```

### Step 3: Update Admin TrainerDocumentType Resource

Similar to Step 2 but for `TrainerDocumentType` model:
- `app/Filament/Admin/Resources/TrainerDocumentTypeRequests/` was deleted in Phase 2
- If no Admin resource exists for `TrainerDocumentType`, create one at `app/Filament/Admin/Resources/TrainerDocumentTypes/TrainerDocumentTypeResource.php`

### Step 4: Update Center CertifiedCenterDocumentType Resource

File: `app/Filament/Center/Resources/CertifiedCenterDocumentTypes/Tables/CertifiedCenterDocumentTypesTable.php`

Replace `documentType.name` with `name`, replace `is_published` toggle with `status` badge:

```php
TextColumn::make('name')
    ->label(__('app.document_type'))
    ->searchable()
    ->getStateUsing(fn ($record) => $record->name),

TextColumn::make('status')
    ->label(__('app.status'))
    ->badge()
    ->color(fn (DocumentTypeRequestStatus $state): string => $state->color()),
```

### Step 5: Update Trainer TrainerDocumentType Resource

File: `app/Filament/Trainer/Resources/TrainerDocumentTypes/Tables/TrainerDocumentTypesTable.php`

Same treatment — replace `is_published` toggle with `status` badge, replace `documentType.name` with `name`.

File: `app/Filament/Trainer/Resources/TrainerDocumentTypes/Schemas/TrainerDocumentTypeForm.php`

Replace `is_published` toggle with status display (or just show `status` as read-only for the trainer):

```php
TextInput::make('key')
    ->label(__('app.document_type_key'))
    ->required()
    ->maxLength(255),

TextInput::make('name')
    ->label(__('app.document_type_name'))
    ->required()
    ->maxLength(255),
```

### Step 6: Add Create Pages for Center/Trainer Panels

If `CreateCertifiedCenterDocumentType.php` doesn't exist in Center panel, create it. Same for `CreateTrainerDocumentType.php` in Trainer panel.

These should auto-fill `certified_center_id`/`trainer_id` from the authenticated user and set `status = 'pending'`.

### Step 7: Update Admin Resource for TrainerDocumentType (if needed)

Create a simple resource at `app/Filament/Admin/Resources/TrainerDocumentTypes/` with list+view pages for admins to approve/reject trainer document types.

### Step 8: Rename and Update AccreditationRequest Filament Resources

The model `AccreditationRequest` has been renamed to `CenterAccreditationRequest` (Phase 3), and the schema changed: `requested_start_date`/`requested_end_date` removed, `accreditation_start_date`/`accreditation_end_date` added.

**Rename files** — Admin and Center panels:
- `app/Filament/Admin/Resources/AccreditationRequests/` → `CenterAccreditationRequests/`
- `app/Filament/Center/Resources/AccreditationRequests/` → `CenterAccreditationRequests/`
- Update all class names, namespaces, `use` imports, and `$model` references to `CenterAccreditationRequest`
- `app/Filament/Admin/Widgets/AccreditationChart.php` — update queries to reference `CenterAccreditationRequest`

**Update Admin AccreditationRequestForm** (`Schemas/AccreditationRequestForm.php`):
Remove `requested_start_date`, `requested_end_date` fields. Add `accreditation_end_date` as a date picker (visible only when approving). `accreditation_start_date` is auto-set to `now()` on approval — not shown in form:

```php
// REMOVED:
// DateTimePicker::make('requested_start_date')
// DateTimePicker::make('requested_end_date')

// ADD: visible when status is being set to approved
DateTimePicker::make('accreditation_end_date')
    ->label(__('app.accreditation_end_date'))
    ->required()
    ->after(now())
    ->visible(fn (string $operation) => $operation === 'edit'),

Textarea::make('request_notes')
    ->label(__('app.request_notes'))
    ->disabled()
    ->dehydrated(false)
    ->columnSpanFull(),
```

**Update Admin AccreditationRequestInfolist** (`Schemas/AccreditationRequestInfolist.php`):
Replace `requested_start_date`/`requested_end_date` with `accreditation_start_date`/`accreditation_end_date`:

```php
TextEntry::make('accreditation_start_date')
    ->label(__('app.accreditation_start_date'))
    ->dateTime()
    ->placeholder(__('app.not_set')),

TextEntry::make('accreditation_end_date')
    ->label(__('app.accreditation_end_date'))
    ->dateTime()
    ->placeholder(__('app.not_set')),
```

**Update Admin AccreditationRequestsTable** (`Tables/AccreditationRequestsTable.php`):
Replace columns and update approve action to accept `accreditation_end_date`:

```php
// Replace column:
TextColumn::make('accreditation_start_date')
    ->label(__('app.accreditation_start_date'))
    ->dateTime()
    ->sortable()
    ->placeholder(__('app.not_set')),

TextColumn::make('accreditation_end_date')
    ->label(__('app.accreditation_end_date'))
    ->dateTime()
    ->sortable()
    ->placeholder(__('app.not_set')),

// Update approve action to set accreditation dates:
Action::make('approve')
    ->label(__('app.approve'))
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->form([
        DateTimePicker::make('accreditation_end_date')
            ->label(__('app.accreditation_end_date'))
            ->required()
            ->after(now()),
    ])
    ->visible(fn (CenterAccreditationRequest $record) => $record->status !== AccreditationStatus::Approved)
    ->action(function (CenterAccreditationRequest $record, array $data): void {
        $record->update([
            'status' => AccreditationStatus::Approved->value,
            'accreditation_start_date' => now(),
            'accreditation_end_date' => $data['accreditation_end_date'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }),
```

**Update Center AccreditationRequestForm** (`Schemas/AccreditationRequestForm.php`):
Remove all date pickers — the center/trainer only submits a request note:

```php
// REMOVED entirely:
// DateTimePicker::make('requested_start_date')
// DateTimePicker::make('requested_end_date')
// Keep only:
Placeholder::make('center_name')
    ->label(__('app.certified_center'))
    ->content($center?->name ?? '-')
    ->columnSpanFull(),

Textarea::make('request_notes')
    ->label(__('app.request_notes'))
    ->rows(4)
    ->columnSpanFull(),
```

**Update Center AccreditationRequestInfolist** (`Schemas/AccreditationRequestInfolist.php`):
Replace `requested_start_date`/`requested_end_date` with `accreditation_start_date`/`accreditation_end_date`:

```php
TextEntry::make('accreditation_start_date')
    ->label(__('app.accreditation_start_date'))
    ->dateTime()
    ->placeholder(__('app.not_set')),

TextEntry::make('accreditation_end_date')
    ->label(__('app.accreditation_end_date'))
    ->dateTime()
    ->placeholder(__('app.not_set')),
```

**Update Center AccreditationRequestsTable** (`Tables/AccreditationRequestsTable.php`):
Same column replacements as Admin table (but without approve actions).

Also update:
- `app/Filament/Admin/ViewAccreditationRequest.php` — update `approve()` private method to set `accreditation_start_date = now()` and accept `accreditation_end_date` from a form:

```php
private function approve(CenterAccreditationRequest $request, array $data): void
{
    $request->update([
        'status' => AccreditationStatus::Approved->value,
        'accreditation_start_date' => now(),
        'accreditation_end_date' => $data['accreditation_end_date'],
        'reviewed_by' => Auth::id(),
        'reviewed_at' => now(),
    ]);
}
```

Add a form to the approve header action:
```php
Action::make('approve')
    ->label(__('app.approve'))
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->form([
        DateTimePicker::make('accreditation_end_date')
            ->label(__('app.accreditation_end_date'))
            ->required()
            ->after(now()),
    ])
    ->visible(fn() => $this->record->status !== AccreditationStatus::Approved)
    ->action(function (array $data): void {
        $this->approve($this->record, $data);
        $this->refreshFormData(['status', 'reviewed_by', 'reviewed_at', 'accreditation_start_date', 'accreditation_end_date']);
    }),
```

**Center CreateAccreditationRequest page** (`Pages/CreateAccreditationRequest.php`):
Already has `mutateFormDataBeforeCreate` that strips admin fields. Keep as-is but remove `requested_start_date`/`requested_end_date` from the form.

**Trainer AccreditationRequest resources** (Admin + Trainer panels):
Files to update:
- `app/Filament/Admin/Resources/TrainerAccreditationRequests/` — forms, tables, infolists
- `app/Filament/Trainer/Resources/TrainerAccreditationRequests/` — forms, tables, infolists
- `app/Filament/Admin/Resources/TrainerAccreditationRequestResource.php` — single-file resource

Apply the same form/infolist/table changes — replace `requested_*` dates with `accreditation_*` dates, update approve actions to set `accreditation_start_date = now()` and prompt for `accreditation_end_date`.

### Step 9: Update Admin Trainer Form — Membership Dates

**File: `app/Filament/Admin/Resources/Trainers/Schemas/TrainerForm.php`**

Add `membership_end_date` as a required date picker. `membership_start_date` is auto-set in the create logic:

```php
// Add to the form schema:
DateTimePicker::make('membership_end_date')
    ->label(__('app.membership_end_date'))
    ->required()
    ->after(now())
    ->columnSpan(1),
```

In `app/Filament/Admin/Resources/Trainers/Pages/CreateTrainer.php`, override `mutateFormDataBeforeCreate`:

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['membership_start_date'] = now();

    return $data;
}
```

### Step 10: Update Certification Forms — Add Source

**Admin CertificationForm** (`app/Filament/Admin/Resources/Certifications/Schemas/CertificationForm.php`):

Add `source` select field:
```php
Select::make('source')
    ->label(__('app.source'))
    ->options(CertificationSource::class)
    ->default('board')
    ->required()
    ->columnSpan(1),
```

**Center CertificationForm** (`app/Filament/Center/Resources/Certifications/Schemas/CertificationForm.php`):

Auto-set `source = 'center'` — either hide the field or set in `mutateFormDataBeforeCreate`:
```php
// In CreateCertification page:
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['source'] = CertificationSource::Center->value;

    return $data;
}
```

**Import logic** (Excel import in `ListCertifications` pages): ensure `source = 'board'` is set when importing from CSV.

### Step 11: Redirect After Create

**Admin CreateCertification** (`app/Filament/Admin/Resources/Certifications/Pages/CreateCertification.php`):

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->record]);
}
```

**Center CreateCertification** (`app/Filament/Center/Resources/Certifications/Pages/CreateCertification.php`):

Same override.

**Admin CreateTrainer** (`app/Filament/Admin/Resources/Trainers/Pages/CreateTrainer.php`):

Redirect to certification create page:
```php
protected function getRedirectUrl(): string
{
    return \App\Filament\Admin\Resources\Certifications\CertificationResource::getUrl('create', [
        'trainer_id' => $this->record->id,
    ]);
}
```

### Step 12: Enable Profile Editing (No Password)

Centers and trainers should be able to edit their name, image, and email but NOT their password (only admin can change passwords).

**Center panel:** The profile menu item is already registered in `CenterPanelProvider`. Customize the profile page by creating a simple edit form that excludes the password field. If using Filament's default profile page, override it:

In `CenterPanelProvider` or `TrainerPanelProvider`:
```php
->profile(CustomCenterProfilePage::class)
```

Create `app/Filament/Center/Pages/CustomCenterProfilePage.php`:
```php
class CustomCenterProfilePage extends ProfilePage
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    TextInput::make('name')->required(),
                    FileUpload::make('logo')->image(),
                    TextInput::make('email')->email(),
                    // NO password field
                ]),
        ];
    }
}
```

Same approach for `TrainerPanelProvider`.

### Step 13: Add Welcome Widget

Add a welcome greeting to Center and Trainer dashboards.

**Create `app/Filament/Center/Widgets/WelcomeWidget.php`:**
```php
<?php

namespace App\Filament\Center\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome';

    public function getUserName(): string
    {
        return auth('certified_center')->user()?->name ?? __('app.guest');
    }
}
```

**Create `resources/views/filament/widgets/welcome.blade.php`:**
```blade
<div class="p-4 text-lg font-semibold">
    {{ __('app.welcome') }}, {{ $this->getUserName() }}!
</div>
```

**Register in `CenterPanelProvider`:**
```php
->widgets([
    WelcomeWidget::class,
    AccreditationStatusBanner::class,
])
```

Same for `TrainerPanelProvider` — create `app/Filament/Trainer/Widgets/WelcomeWidget.php` (uses `auth('trainer')`), register in provider.

## Acceptance Criteria

- [ ] Admin `DocumentTypeResource` has no `ApprovedCentersRelationManager`
- [ ] Admin `CertifiedCenterDocumentTypeResource` shows `name` (not `documentType.name`), shows `status` badge, has approve/reject actions
- [ ] Center `CertifiedCenterDocumentTypeResource` shows `name`, shows `status` badge (read-only for center)
- [ ] Trainer `TrainerDocumentTypeResource` shows `name`, shows `status` badge (read-only for trainer)
- [ ] No Filament file references `is_published` for document type models
- [ ] No Filament file references `documentType.name` for center/trainer document types
- [ ] All panels render without errors
- [ ] Admin `CenterAccreditationRequests` resource replaces old `AccreditationRequests` resource
- [ ] Center `CenterAccreditationRequests` resource replaces old `AccreditationRequests` resource
- [ ] Widget `AccreditationChart` queries `CenterAccreditationRequest`
- [ ] Admin AccreditationRequest form has `accreditation_end_date` (not `requested_*` dates)
- [ ] Center AccreditationRequest form has no date fields (just notes + submit)
- [ ] Admin approve action sets `accreditation_start_date = now()` and prompts for `accreditation_end_date`
- [ ] Admin Trainer form includes `membership_end_date` (required, after now)
- [ ] `CreateTrainer` auto-sets `membership_start_date = now()`
- [ ] Admin Certification form has `source` select field
- [ ] Center Certification auto-sets `source = 'center'`
- [ ] CreateCertification redirects to view page
- [ ] CreateTrainer redirects to Certification create page
- [ ] Center/Trainer profile editing excludes password field
- [ ] Center/Trainer dashboard shows WelcomeWidget
