# International Board — National Certification Authority

Laravel 12 + Filament 4 application for managing professional certifications, accredited training centers, and certified trainers. Multilingual (EN/AR) public website with three admin panels.

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
composer dev    # starts server + queue + vite + pail
```

**Login:** Admin panel at `/admin`, Center at `/center`, Trainer at `/trainer`. Credentials from `UserSeeder` / `CertifiedCenterSeeder` / `TrainerSeeder`.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.5 |
| Admin Panels | Filament 4 (Admin / Center / Trainer) |
| Frontend | Vite 7, Tailwind 4 |
| DB (dev) | SQLite (`database/database.sqlite`) |
| DB (prod) | MySQL (Hostinger) |
| Translations | `spatie/laravel-translatable` |
| Static Analysis | PHPStan (Larastan) |
| Code Style | Laravel Pint |

## Project Structure

```
├── app/
│   ├── Enums/                    # AccreditationStatus, CenterStatus, PanelId, etc.
│   ├── Filament/
│   │   ├── Admin/Resources/      # 19 resources (users, centers, trainers, certifications, ...)
│   │   ├── Center/Resources/     # 6 resources (certifications, trainers, doc types, ...)
│   │   └── Trainer/Resources/    # 4 resources (certifications, doc types, financial, accreditation)
│   ├── Http/
│   │   ├── Controllers/Web/      # Public site controllers (home, certifications, centers, trainers, blog, memberships, contact)
│   │   └── Middleware/           # EnsureTrainerIsAccredited, EnsureCenterIsAccredited, LocaleMiddleware
│   ├── Models/                   # 18 Eloquent models
│   ├── Observers/                # 5 observers (Certification, Trainer, CertifiedCenter, accreditation requests)
│   ├── Policies/                 # 6 policies
│   ├── Providers/                # Service registration, repository binding, observer registration
│   ├── Repositories/             # 8 repositories (query logic, filters, pagination)
│   └── Services/                 # 19 services (domain orchestration)
├── config/panels.php             # Panel configuration (id, path, guard, color, namespaces)
├── database/
│   ├── factories/                # Model factories for all domain models
│   ├── migrations/               # 23 migrations
│   └── seeders/                  # 21 seeders
├── lang/
│   ├── en/                       # English translations (app.php, web.php)
│   └── ar/                       # Arabic translations (app.php, web.php)
├── resources/views/web/          # 27 blade templates for public site
├── routes/
│   ├── web.php                   # Public site routes (/web/*)
│   └── admin.php                 # Admin routes (/exports/*)
└── tests/                        # PHPUnit wired, tests empty (ready for TDD)
```

## Architecture

### Three Filament Panels

| Panel | Path | Guard | Model | Purpose |
|---|---|---|---|---|
| Admin | `/admin` | `web` | `User` | Full CRUD for all entities, approval workflows |
| Center | `/center` | `certified_center` | `CertifiedCenter` | Center-scoped: certifications, trainers, doc types, financial requests |
| Trainer | `/trainer` | `trainer` | `Trainer` | Trainer-scoped: certifications, doc types, financial requests |

Config in `config/panels.php`. Each panel has its own `PanelProvider` in `app/Providers/Filament/`.

### Request Flow (Public Site)

```
Http/Controllers/Web/** → Service (app/Services/) → Repository (app/Repositories/) → Model
```

Services and repositories are singletons, registered in `RepositoryServiceProvider` and `ServiceRegistrationProvider`.

### Polymorphic Relationships

**Certification** is the core entity with two polymorphic relations:
- `creator()` → `User` | `Trainer` | `CertifiedCenter` (who issued it)
- `documentable()` → `DocumentType` | `TrainerDocumentType` | `CertifiedCenterDocumentType` (what type)

### Multilingual

All document type models (`DocumentType`, `TrainerDocumentType`, `CertifiedCenterDocumentType`) use `HasTranslations` with `$translatable = ['name']` — names stored as JSON, accessed via `->getTranslation('name', app()->getLocale())`.

Public site locale switchable via `GET /web/lang/{locale}`.

## Public Website Routes

| Route | Page |
|---|---|
| `GET /web` | Home |
| `GET /web/certifications` | Verify certifications |
| `GET /web/certifications/{serial}` | Certification detail |
| `GET /web/centers` | List accredited centers |
| `GET /web/centers/{id}` | Center detail (logo, info, certifications, doc types) |
| `GET /web/trainers` | List certified trainers |
| `GET /web/trainers/{id}` | Trainer detail (avatar, info, specializations, certifications) |
| `GET /web/trainers/evaluation` | Trainer evaluation page |
| `GET /web/blog` | Blog posts |
| `GET /web/memberships` | Membership plans |
| `POST /web/contact` | Contact form |

## Filament Resource Index

### Admin (19 resources)
`UserResource`, `CertifiedCenterResource`, `TrainerResource`, `CertificationResource`, `TraineeResource`, `CountryResource`, `SpecializationResource`, `DocumentTypeResource`, `TrainerDocumentTypeResource`, `CertifiedCenterDocumentTypeResource`, `TrainerAccreditationRequestResource`, `CenterAccreditationRequestResource`, `TrainerFinancialRequestResource`, `CertifiedCenterFinancialRequestResource`, `PaymentAgentPersonResource`, `MembershipResource`, `BlogPostResource`, `StaticPageResource`, `ApplicationSettingResource`, `ContactMessageResource`

### Center (6 resources)
`CertificationResource`, `TrainerResource`, `TraineeResource`, `CertifiedCenterDocumentTypeResource`, `CenterAccreditationRequestResource`, `CenterFinancialRequestResource`, `CenterProfilePage`

### Trainer (4 resources)
`CertificationResource`, `TrainerDocumentTypeResource`, `TrainerAccreditationRequestResource`, `TrainerFinancialRequestRequest`, `TrainerProfilePage`

## Model Reference

### Core Entities
- **Certification** — `certifications` table. Polymorphic `creator` + `documentable`. Auto-generates `document_code`, `accredited_serial_number`, and `accreditation_number`.
- **Trainer** — `trainers` table. Belongs to Country, Center. Many-to-many Specializations. Auto-generates `accreditation_number`.
- **CertifiedCenter** — `certified_centers` table. Has many Trainers, Certifications, DocumentTypes. Auto-generates `accreditation_number`.
- **Trainee** — `trainees` table. Belongs to Country. Has many Certifications.

### Document Types (three-tier)
- **DocumentType** — `board_document_types` — Admin-managed board document types
- **TrainerDocumentType** — `trainer_document_types` — Trainer-submitted, admin-approved
- **CertifiedCenterDocumentType** — `certified_center_document_types` — Center-submitted, admin-approved

All three use `HasTranslations` for multilingual `name` field.

### Accreditation Flow
1. Trainer/Center submits `TrainerAccreditationRequest` / `CenterAccreditationRequest`
2. Admin reviews → Approve/Reject
3. On approve: trainer/center accreditation period stamped
4. Observers prevent duplicate active requests and time overlaps

### Financial Requests
- **TrainerFinancialRequest** — trainer → payment agent person
- **CertifiedCenterFinancialRequest** — center → payment agent person

## Commands

```bash
composer dev            # Server + queue + vite + pail
composer test           # Run tests
composer fix            # Format with Pint
composer ci-check:quick # Pint + PHPStan level 1
composer ci-check:full  # Full CI gate
```

## Deployment

Single branch: `production`. Push to deploy via GitHub Actions.

```bash
git push origin production                    # Standard deploy
git commit -m "[migrate:fresh] ..." && git push origin production  # Destructive deploy
```

Destructive keywords: `[migrate:fresh]`, `[destructive]`, `[reset-db]`, `[fresh]`.

## For AI Agents

This project is agent-ready. Key files:

- **`CLAUDE.md`** — Full architecture, conventions, feature index with model relationships, Filament resources, services, enums, observers
- **`AGENTS.md`** — Agent skills and graphify knowledge graph instructions
- **`config/panels.php`** — Panel configuration (read before modifying any panel)
- **`app/Providers/RepositoryServiceProvider.php`** — Repository bindings
- **`app/Providers/ServiceRegistrationProvider.php`** — Service bindings
- **`app/Providers/ObserverServiceProvider.php`** — Observer registrations

When adding a new domain:
1. Create Model in `app/Models/` with `$table`, `$fillable`, relationships
2. Create migration in `database/migrations/`
3. Create factory + seeder
4. Create Repository in `app/Repositories/<Domain>/`
5. Create Service in `app/Services/<Domain>/`
6. Register both in providers
7. Create Filament Resource in each panel that needs it (`Pages/`, `Schemas/`, `Tables/`)
8. Add public routes + controller + views if needed
9. Add translation keys to `lang/en/app.php` and `lang/ar/app.php`
10. Run `composer fix` and `vendor/bin/phpstan analyse app --memory-limit=1G`
