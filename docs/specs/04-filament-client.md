# Filament Center & Trainer Panels

> **Note:** There is no "Client" panel in this project. This document covers the **Center** and **Trainer** panels (filename `04-filament-client.md` retained per project convention).

## Center panel

**Path:** `/center`  
**Provider:** `App\Providers\Filament\CenterPanelProvider`  
**Guard:** `certified_center`  
**Model:** `App\Models\CertifiedCenter`  
**Color:** `blue`

### Middleware & access

- `EnsureCenterIsAccredited` on panel middleware stack
- `AccreditationGateService` on resources for fine-grained actions
- Widget: `AccreditationStatusBanner` (explicit registration)

### Resources (7)

| Resource | Model | Scoping |
|----------|-------|---------|
| `AccreditationRequestResource` | `AccreditationRequest` | Own center only; create blocked if active request exists |
| `CertificationResource` | `Certification` | `certified_center_id = AUth::id()`; gated when not accredited |
| `TraineeResource` | `Trainee` | Via `whereHas('certifications', ... center_id ...)` |
| `CenterTypeRequestResource` | `CenterTypeRequest` | Own center |
| `CenterDocumentTypeRequestResource` | `CenterDocumentTypeRequest` | Own center |
| `CenterFinancialRequestResource` | `CertifiedCenterFinancialRequest` | Own center |

### Widgets

- `AccreditationStatusBanner`
- `CenterStatsOverview`, `MonthlyCertificationsChart` (discovered)

---

## Trainer panel

**Path:** `/trainer`  
**Provider:** `App\Providers\Filament\TrainerPanelProvider`  
**Guard:** `trainer`  
**Model:** `App\Models\Trainer`  
**Color:** `emerald` (resolved via `ResolvesFilamentColor`)

### Middleware & access

- `EnsureTrainerIsAccredited` in **auth** middleware stack
- All resources: `getEloquentQuery()` → `where('trainer_id', AUth::id())`

### Resources (3)

| Resource | Model | Purpose |
|----------|-------|---------|
| `TrainerAccreditationRequestResource` | `TrainerAccreditationRequest` | Submit/view accreditation |
| `TrainerDocumentTypeRequestResource` | `TrainerDocumentTypeRequest` | Request document types; pending badge |
| `TrainerFinancialRequestResource` | `TrainerFinancialRequest` | Financial history |

### Widgets

None registered under `app/Filament/Trainer/Widgets`.

---

## Shared patterns (Center + Trainer)

### Query scoping

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('trainer_id', auth('trainer')->id());
    // or certified_center_id for center panel
}
```

### Resource `can*()` overrides

Used with `AccreditationGateService` to disable create/edit when accreditation is missing or pending.

### Password brokers

| Panel | Broker |
|-------|--------|
| Center | `certified_centers` |
| Trainer | `trainers` |

## Panel comparison

| | Admin | Center | Trainer |
|---|-------|--------|---------|
| Resources | 20 | 7 | 3 |
| Scope | Global | Single center | Single trainer |
| Accreditation | Reviews others | Must be accredited | Must be accredited |
| Financial | All requests | Own center | Own trainer |

## Config reference

```php
// config/panels.php
'center' => [
    'guard' => 'certified_center',
    'password_broker' => 'certified_centers',
    ...
],
'trainer' => [
    'guard' => 'trainer',
    'password_broker' => 'trainers',
    'color' => 'emerald',
    ...
],
```
