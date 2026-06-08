# Phase 8: Behavioral Rules & UI Updates

## Title

Redirects, Profile Editing, Welcome Message, and Import Rules

## Description

Cross-cutting behavioral changes that don't fit into schema/model/resource updates: redirect after create, profile editing without password, welcome greeting, and import source rules.

## Vision

A cohesive user experience where creation flows lead to logical next pages, profile editing is restricted to non-sensitive fields, and each login greets the user by name. Imported data is consistently tagged with the correct source.

## Tips

- Most changes are in Filament `Pages` (redirect overrides) and `PanelProvider` (widget registration).
- Profile customization may require creating custom profile page classes if the default Filament profile includes password fields.
- The import handler (`CertificationsImportHandler`) may be in `app/Imports/` — check if it explicitly sets `source`.

## Steps

### Step 1: Redirect After Create

**CreateCertification (Admin + Center panels)** — override `getRedirectUrl()` to view page:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->record]);
}
```

**CreateTrainer (Admin panel)** — redirect to Certification create page:

```php
protected function getRedirectUrl(): string
{
    return \App\Filament\Admin\Resources\Certifications\CertificationResource::getUrl('create', [
        'trainer_id' => $this->record->id,
    ]);
}
```

### Step 2: Enable Profile Editing (No Password)

**Center panel:** Customize profile page to exclude password field:

```php
// app/Filament/Center/Pages/CustomCenterProfilePage.php
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

Register in `CenterPanelProvider`:
```php
->profile(CustomCenterProfilePage::class)
```

**Trainer panel:** Same approach for `TrainerPanelProvider`.

### Step 3: Add Welcome Widget

**Create `app/Filament/Center/Widgets/WelcomeWidget.php`** and `app/Filament/Trainer/Widgets/WelcomeWidget.php`:

```php
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

**Register** in `CenterPanelProvider` and `TrainerPanelProvider` arrays.

### Step 4: Import Source Rules

All certifications imported from Excel must have `source = 'board'`. The import handler (likely `app/Imports/CertificationsImportHandler.php`) should set this:

```php
$certification->source = CertificationSource::Board->value;
```

If the import uses a batch insert, add `'source' => 'board'` to the data array.

## Acceptance Criteria

- [ ] Admin CreateCertification redirects to view page
- [ ] Center CreateCertification redirects to view page
- [ ] Admin CreateTrainer redirects to Certification create page (with trainer_id)
- [ ] Center profile editing excludes password field
- [ ] Trainer profile editing excludes password field
- [ ] Center dashboard shows "Welcome, [name]" widget
- [ ] Trainer dashboard shows "Welcome, [name]" widget
- [ ] Excel imports set `source = 'board'`
