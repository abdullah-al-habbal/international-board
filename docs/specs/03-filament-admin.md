# Filament Admin Panel

**Path:** `/admin`  
**Provider:** `App\Providers\Filament\AdminPanelProvider`  
**Guard:** `web` (default)  
**User model:** `App\Models\User` (`UserType::admin` for panel access)

## Features

- SPA mode, login, profile menu item
- Widgets: `StatsOverview`, `AccreditationChart`, `CertificationChart`
- Language switch: `ar`, `en` (panels only)
- Color: `amber` from `config/panels.php`

## Resource layout pattern

```
{Resource}/
  {Resource}Resource.php
  Schemas/{Name}Form.php
  Schemas/{Name}Infolist.php   # when view page exists
  Tables/{Name}Table.php
  Pages/List*, Create*, Edit*, View*
  RelationManagers/            # select resources
```

Static `configure()` methods on Schema/Table classes.

## Resources (20)

| Resource | Model | CRUD notes |
|----------|-------|------------|
| `AccreditationRequestResource` | `AccreditationRequest` | Review center accreditation; nav badge = pending count |
| `TrainerAccreditationRequestResource` | `TrainerAccreditationRequest` | Review trainer accreditation; pending badge |
| `CertifiedCenterResource` | `CertifiedCenter` | Full CRUD; relation managers for doc types |
| `CertificationResource` | `Certification` | All certifications (eager-loaded relations) |
| `TraineeResource` | `Trainee` | Global trainee registry |
| `TrainerResource` | `Trainer` | Trainer accounts |
| `DocumentTypeResource` | `DocumentType` | Master document types; `ApprovedCentersRelationManager` |
| `CertifiedCenterDocumentTypeResource` | `CertifiedCenterDocumentType` | **List only** (pivot approvals) |
| `CenterDocumentTypeRequestResource` | `CenterDocumentTypeRequest` | Review center doc type requests |
| `CenterTypeRequestResource` | `CenterTypeRequest` | Center type change requests |
| `CountryResource` | `Country` | Reference data |
| `PaymentAgentPersonResource` | `CertifiedCenterPaymentAgentPerson` | Payment agents |
| `CertifiedCenterFinancialRequestResource` | `CertifiedCenterFinancialRequest` | Center financial requests |
| `UserResource` | `User` | Admin users |
| `BlogPostResource` | `BlogPost` | CMS blog; list/create/edit |
| `StaticPageResource` | `StaticPage` | Website static pages |
| `ApplicationSettingResource` | `ApplicationSetting` | Settings; **no create/delete** |
| `Users\UserResource` | — | (listed above) |

Navigation groups use `__('filament.*')` and `__('app.*')` for financial and accreditation groups.

## Relation managers

| Parent resource | Managers |
|-----------------|----------|
| `CertifiedCenterResource` | `ApprovedDocumentTypesRelationManager`, `DocumentTypeRequestsRelationManager` |
| `DocumentTypeResource` | `ApprovedCentersRelationManager` |

## Authorization

- No inline `authorize()` in resource files
- Laravel policies on models (`#[UsePolicy]` and `AuthServiceProvider`)
- Admin user checks via `User::isAdmin()` in policies
- Full access to all centers/trainers/certifications (no tenant scoping)

## Navigation badges

Examples:

- Pending accreditation counts on request resources
- Certified center expiry warnings via services

## Discovery paths

From `config/panels.php`:

- Resources: `App\Filament\Admin\Resources`
- Pages: `App\Filament\Admin\Pages` (empty — uses default Dashboard)
- Widgets: `App\Filament\Admin\Widgets`

## Related config

```php
// config/panels.php
'admin' => [
    'id' => 'admin',
    'path' => '/admin',
    'color' => 'amber',
    ...
],
```
