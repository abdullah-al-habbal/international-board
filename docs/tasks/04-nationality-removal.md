# Phase 4: Nationality Removal

## Title

Remove `nationality` from Certification Model and All Code References

## Description

The `nationality` column no longer exists on the `certifications` table. Any code that reads or writes `$certification->nationality` must be updated to use the `Country` relationship: `$certification->country?->nationality`.

## Vision

Zero references to `$certification->nationality` in the codebase. Nationality is always accessed through the `country` relationship. The `byNationality` scope queries via `whereHas('country', ...)`.

## Tips

- Use grep to find all occurrences before editing: `grep -rn "nationality" app/ resources/ --include="*.php" --include="*.blade.php"`
- The `byNationality` scope on `Certification` model must be rewritten — it currently does `$query->where('nationality', ...)`.
- The import handler stores `nationality` on the Certification row AND on the Trainee model. Only the Certification line should be removed.
- Filament filters that query `Certification::whereNotNull('nationality')->distinct()->pluck(...)` must be rewritten to pull from `Country` model instead.
- Trainee's `nationality` field is NOT affected — that's a different column on a different table.

## Steps

### Step 1: Update `app/Models/Certification.php`

Remove `'nationality'` from `$fillable`:

```php
protected $fillable = [
    'certified_center_id',
    'trainee_id',
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
```

Rewrite `byNationality` scope:

```php
#[Scope]
protected function byNationality(Builder $query, string $nationality): void
{
    $query->whereHas('country', function (Builder $q) use ($nationality) {
        $q->where('nationality', $nationality);
    });
}
```

### Step 2: Update Admin Certifications Table

File: `app/Filament/Admin/Resources/Certifications/Tables/CertificationsTable.php`

Change nationality column (lines ~46-52):

```php
TextColumn::make('country.nationality')
    ->label(__('app.nationality'))
    ->searchable()
    ->badge()
    ->color('info')
    ->toggleable()
    ->getStateUsing(fn ($record) => $record->country?->nationality ?: __('app.no_nationality')),
```

Change nationality filter (lines ~177-186):

```php
SelectFilter::make('nationality')
    ->label(__('app.nationality'))
    ->relationship('country', 'nationality')
    ->searchable()
    ->preload()
    ->placeholder(__('app.filter_select_placeholder')),
```

### Step 3: Update Center Certifications Table

File: `app/Filament/Center/Resources/Certifications/Tables/CertificationsTable.php`

Change nationality filter (lines ~128-135):

```php
SelectFilter::make('nationality')
    ->label(__('app.nationality'))
    ->relationship('country', 'nationality')
    ->searchable()
    ->preload(),
```

### Step 4: Update Certification Export Handler

File: `app/Services/Certification/CertificationExportHandler.php`

Line 75:

```php
$certification->country?->nationality,   // was: $certification->nationality,
```

### Step 5: Update Center ListCertifications Export

File: `app/Filament/Center/Resources/Certifications/Pages/ListCertifications.php`

Line 65:

```php
$certification->country?->nationality,   // was: $certification->nationality,
```

### Step 6: Update Blade View

File: `resources/views/web/certifications/_result.blade.php`

Lines ~115-123:

```blade
@if($certification->country?->nationality)
    <span class="badge bg-dark">
        {{ $certification->country->nationality }}
    </span>
@else
    <span class="badge bg-secondary opacity-75">
        {{ __('web.labels.not_assigned') }}
    </span>
@endif
```

### Step 7: Update Import Handler

File: `app/Imports/CertificationsImportHandler.php`

Remove `'nationality'` from the return array in `processRow()` (line ~45):

```php
return [
    'trainee_id' => $this->getOrCreateTrainee($traineeNameRaw, $nationalityRaw),
    // REMOVED: 'nationality' => $this->cleanNationality($nationalityRaw),
    'accredited_serial_number' => $serialNumber,
    'document_code' => $this->cleanValue($documentCodeRaw),
    'accreditation_number' => $this->cleanValue($accreditationNumberRaw),
    'document_type_id' => $this->getDocumentTypeId($documentTypeRaw),
    'accreditation_date' => $this->parseDate($accreditationDateRaw),
    'trainer_id' => $this->getOrCreateTrainer($trainerNameRaw),
    'country_id' => $this->getOrCreateCountry($nationalityRaw),
    'paper_received' => $this->normalizePaperStatus($paperReceivedRaw),
    'notes' => $this->cleanValue($notesRaw),
    'created_at' => now(),
    'updated_at' => now(),
];
```

### Step 8: Update Factory

File: `database/factories/CertificationFactory.php`

Remove the nationality line:

```php
// REMOVE: 'nationality' => fake()->country(),
```

## Acceptance Criteria

- [ ] `'nationality'` removed from `Certification::$fillable`
- [ ] `byNationality` scope uses `whereHas('country', ...)`
- [ ] Admin CertificationsTable uses `country.nationality` column and filter
- [ ] Center CertificationsTable uses `country.nationality` filter
- [ ] Export handler uses `$certification->country?->nationality`
- [ ] ListCertifications export uses `$certification->country?->nationality`
- [ ] Blade view uses `$certification->country?->nationality`
- [ ] Import handler does not set `'nationality'` on certification rows
- [ ] Factory does not generate `'nationality'`
- [ ] `grep -rn "->nationality" app/ --include="*.php"` shows only Trainee and Country references (no Certification)
