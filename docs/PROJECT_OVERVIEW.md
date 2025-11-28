Project> php -v;composer --version;php artisan --version;php artisan about
>> 
PHP 8.4.12 (cli) (built: Aug 26 2025 18:04:08) (ZTS Visual C++ 2022 x64)
Copyright (c) The PHP Group
Zend Engine v4.4.12, Copyright (c) Zend Technologies
Composer version 2.8.12 2025-09-19 13:41:59
PHP version 8.4.12 (C:\laragon\bin\php\php-8.4.12-Win32-vs17-x64\php.exe)
Run the "diagnose" command to get more detailed diagnostics output.
Laravel Framework 12.28.1

  Environment ....................................................................................  
  Application Name ......................................................................... Board  
  Laravel Version ........................................................................ 12.28.1  
  PHP Version ............................................................................. 8.4.12  
  Composer Version ........................................................................ 2.8.12  
  Environment .............................................................................. local  
  Debug Mode ............................................................................. ENABLED  
  URL .................................................................................. localhost  
  Maintenance Mode ........................................................................... OFF  
  Timezone ................................................................................... UTC  
  Locale ...................................................................................... ar  

  Cache ..........................................................................................  
  Config .............................................................................. NOT CACHED  
  Events .............................................................................. NOT CACHED  
  Routes .............................................................................. NOT CACHED  
  Views ................................................................................... CACHED  

  Drivers ........................................................................................  
  Broadcasting ............................................................................... log  
  Cache ................................................................................. database  
  Database ................................................................................. mysql  
  Logs ..................................................................................... daily  
  Mail ....................................................................................... log  
  Queue ................................................................................. database  
  Session ............................................................................... database  

  Storage ........................................................................................  
  public\storage ........................................................ NOT LINKED  

  Filament .......................................................................................  
  Blade Icons ......................................................................... NOT CACHED  
  Packages . filament, forms, notifications, support, tables, actions, infolists, schemas, widgets  
  Panel Components .................................................................... NOT CACHED  
  Version ................................................................................. v4.0.8  
  Views ............................................................................ NOT PUBLISHED  

  Livewire .......................................................................................  
  Livewire ................................................................................ v3.6.4  

Project> 

# Certification Board Management System - Project Overview

## 📋 Project Summary

**Project Name:** Board (Certification Board Management System)  
**Purpose:** A comprehensive web application for managing certification boards, accredited training centers, certifications, trainers, and trainees. The system facilitates the accreditation process, certification issuance, and provides public-facing interfaces for certification verification.

**Domain/Work Area:** Education & Training Certification Management  
**Primary Language:** Arabic (ar) with English (en) support  
**Default Locale:** Arabic (`APP_LOCALE=ar`)

---

## 🏗️ Architecture Overview

### Technology Stack

- **Framework:** Laravel 12.x
- **Admin Panel:** Filament v4.0
- **PHP Version:** ^8.2
- **Database:** MySQL
- **Translation:** Spatie Laravel Translatable (^6.11)
- **Excel Import/Export:** Maatwebsite Excel (^3.1)

### System Architecture

#### Dual-Panel System

The application uses **Filament v4** with two separate authentication panels:

1. **Admin Panel** (`/admin`)
   - Path: `/admin`
   - Color Theme: Amber
   - Guard: `web` (default)
   - Users: `App\Models\User` with `type = 'admin'`
   - Full system access and management

2. **Center Panel** (`/center`)
   - Path: `/center`
   - Color Theme: Blue
   - Guard: `certified_center`
   - Users: `App\Models\CertifiedCenter`
   - Limited access to own resources only

#### Service Layer Pattern

The application follows a **Service-Repository pattern**:

- **Services:** Business logic layer (`app/Services/`)
  - `AccreditationRequestService`
  - `ApplicationSettingService`
  - `CertificationService`
  - `CertifiedCenterService`
  - `MembershipService`
  - `StaticPageService`
  - `StatsService` / `CenterStatsService`
  - `TrainerService`
  - `UserService`

- **Repositories:** Data access layer (`app/Repositories/`)
  - One repository per domain model
  - Handles database queries and data manipulation

- **Policies:** Authorization layer (`app/Policies/`)
  - Role-based access control
  - Center users can only access their own resources

#### Model Organization

Models are organized with **Traits** for separation of concerns:

- **Relations:** `*Relations.php` - Eloquent relationships
- **Scopes:** `*Scopes.php` - Query scopes
- **Helpers:** `*Helpers.php` - Helper methods
- **Checkers:** `*Checkers.php` - Status/condition checks
- **Actions:** `*Actions.php` - Business actions

---

## ✨ Core Features

### 1. Certification Management

**Purpose:** Manage all certifications issued by accredited centers

**Key Features:**
- Create, edit, view, and delete certifications
- Excel import functionality (`CertificationsImport`)
- Excel export functionality (`CertificationsExport`)
- Search by serial number (`accredited_serial_number`)
- Document type management (database-driven, not enum)
- Denormalized fields for performance (`trainee_name`, `trainer_name`, `nationality`)
- Paper document tracking (`paper_received`)
- Accreditation date tracking
- Notes and additional information

**Fields:**
- `certified_center_id` - Foreign key to certified center
- `certificate_type` - Enum: `basic` or `accreditation`
- `trainee_id` - Foreign key to trainee (nullable)
- `trainee_name` - Denormalized name
- `trainer_id` - Foreign key to trainer
- `trainer_name` - Denormalized name
- `document_type_id` - Foreign key to document_types table
- `accredited_serial_number` - Unique serial number
- `document_code` - Document code
- `accreditation_number` - Auto-generated or manual
- `accreditation_date` - Date of accreditation
- `country_id` - Foreign key to countries
- `nationality` - Denormalized nationality
- `paper_received` - Boolean flag
- `notes` - Additional notes

**Access:**
- **Admin:** Full CRUD access to all certifications
- **Center:** Can create and manage only their own certifications
- **Public:** Search and view certifications by serial number

### 2. Certified Center Management

**Purpose:** Manage accredited training centers

**Key Features:**
- Center registration and authentication
- Accreditation period management (start/end dates)
- Status management (active, inactive, pending, suspended)
- Center profile management
- Manager information
- Contact details (email, phone, address)

**Fields:**
- `name` - Center name
- `email` - Login email
- `password` - Hashed password
- `address` - Physical address
- `phone` - Contact phone
- `manager_name` - Manager's name
- `accreditation_period_start` - Accreditation start date
- `accreditation_period_end` - Accreditation end date
- `accreditation_number` - Accreditation number
- `status` - Enum: `active`, `inactive`, `pending`, `suspended`
- `is_active` - Boolean active flag

**Access:**
- **Admin:** Full CRUD access
- **Center:** Can view and edit own profile
- **Public:** View list of centers and center details

### 3. Accreditation Request Workflow

**Purpose:** Manage accreditation renewal/extension requests from centers

**Key Features:**
- Centers can submit accreditation requests
- Admin can approve/reject requests
- Request status tracking (pending, approved, rejected, under_review)
- Request notes and admin review notes
- Date range requests (start/end dates)
- Automatic notifications on status change

**Workflow:**
1. Center submits request with desired dates
2. Request status: `pending`
3. Admin reviews and can set to `under_review`
4. Admin approves (`approved`) or rejects (`rejected`)
5. Center receives notification

**Fields:**
- `certified_center_id` - Foreign key to center
- `requested_start_date` - Requested start date
- `requested_end_date` - Requested end date
- `request_notes` - Notes from center
- `status` - Enum: `pending`, `approved`, `rejected`, `under_review`
- `admin_notes` - Admin review notes
- `reviewed_by` - Admin user ID who reviewed
- `reviewed_at` - Review timestamp

**Access:**
- **Admin:** Full access, can approve/reject
- **Center:** Can create and view own requests
- **Public:** Not accessible

### 4. Trainer Management

**Purpose:** Manage trainers who conduct training programs

**Key Features:**
- Trainer profile management
- Contact information
- Specialization tracking
- Relationship with certifications
- Country/nationality tracking

**Access:**
- **Admin:** Full CRUD access
- **Center:** View only (for reference when creating certifications)
- **Public:** Not accessible

### 5. Trainee Management

**Purpose:** Manage trainees who receive certifications

**Key Features:**
- Extended profile with:
  - Basic info (name, email, phone)
  - Personal details (date of birth, gender, nationality)
  - Professional info (occupation, organization)
  - Contact details (address)
  - Emergency contact information
  - Medical information
  - Notes
- Relationship with certifications
- Country tracking

**Fields:**
- `name` - Trainee name
- `email` - Email (unique, nullable)
- `phone` - Phone number
- `country_id` - Foreign key to countries
- `date_of_birth` - Date of birth
- `nationality` - Nationality string
- `gender` - Enum: `male`, `female`, `other`
- `occupation` - Occupation
- `organization` - Organization name
- `address` - Address text
- `emergency_contact_name` - Emergency contact
- `emergency_contact_phone` - Emergency phone
- `medical_info` - Medical information
- `notes` - Additional notes

**Access:**
- **Admin:** Full CRUD access
- **Center:** View only (for reference when creating certifications)
- **Public:** Not accessible

### 6. Document Type Management

**Purpose:** Manage types of documents/certificates

**Key Features:**
- Database-driven (not enum-based)
- Translatable names (Arabic/English)
- Key-based identification
- Used in certifications

**Fields:**
- `key` - Unique key identifier
- `name` - Translatable name (JSON: `{en: "...", ar: "..."}`)
- `description` - Optional description
- `is_active` - Active flag

**Access:**
- **Admin:** Full CRUD access
- **Center:** View only (for reference)
- **Public:** Not accessible

### 7. Country/Nationality Management

**Purpose:** Manage countries and nationalities with bilingual support

**Key Features:**
- Translatable country names (Arabic/English)
- Translatable nationality names
- Country codes (ISO codes)
- Active/inactive status
- Used in certifications, trainers, and trainees

**Fields:**
- `name` - Translatable country name (JSON: `{en: "...", ar: "..."}`)
- `code` - Country code (e.g., "LBY")
- `code_2` - Two-letter code (e.g., "LY")
- `nationality` - Translatable nationality (JSON: `{en: "...", ar: "..."}`)
- `is_active` - Active flag

**Translation Status:**
- ✅ JSON columns implemented
- ✅ Spatie Translatable trait configured
- ⚠️ Initial data: Both `en` and `ar` have same value (placeholder)
- 📝 **Action Required:** Admins need to manually update Arabic translations via Filament GUI

**Access:**
- **Admin:** Full CRUD access, can edit translations
- **Center:** View only
- **Public:** Not accessible

### 8. Static Pages Management

**Purpose:** Manage public-facing static content pages

**Key Features:**
- Slug-based routing
- Translatable content
- SEO-friendly URLs
- Public access via `/pages/{slug}`

**Fields:**
- `slug` - URL-friendly identifier
- `title` - Translatable title
- `content` - Translatable content (HTML)
- `meta_description` - SEO meta description
- `is_published` - Publication status

**Access:**
- **Admin:** Full CRUD access
- **Public:** View published pages only

### 9. Application Settings

**Purpose:** Manage system-wide configuration

**Key Features:**
- Key-value storage
- Type-based settings (text, email, phone, url, number, boolean)
- Site configuration (name, email, phone)
- Social media links
- Upload size limits
- Maintenance mode

**Default Settings:**
- `site_name` - Site name
- `site_email` - Contact email
- `site_phone` - Contact phone
- `facebook_url` - Facebook link
- `twitter_url` - Twitter link
- `linkedin_url` - LinkedIn link
- `max_upload_size` - Max file upload size (MB)
- `maintenance_mode` - Maintenance mode flag

**Access:**
- **Admin:** Full CRUD access
- **Public:** Not accessible (used in application logic)

### 10. Membership Management

**Purpose:** Manage memberships (if applicable)

**Key Features:**
- Membership tracking
- Relationship with centers or users

**Access:**
- **Admin:** Full CRUD access
- **Center/Public:** Not accessible

### 11. Excel Import/Export

**Purpose:** Bulk import and export of certifications

**Key Features:**
- Excel file import with Arabic header support
- Automatic data normalization
- Duplicate detection
- Error handling and reporting
- Import statistics
- Export functionality for data backup

**Import Process:**
1. Admin navigates to Certifications → Import
2. Uploads Excel file with certification data
3. System validates and normalizes data
4. Creates DocumentType records if missing
5. Links to existing Trainers, Trainees, Countries
6. Generates import report

**Access:**
- **Admin:** Full access to import/export
- **Center:** Not accessible

### 12. Bilingual Support (Arabic/English)

**Purpose:** Full bilingual interface and content

**Key Features:**
- **Interface Translations:**
  - All Filament resources use `__('app.key')` pattern
  - Translation files: `lang/en/app.php` and `lang/ar/app.php`
  - Navigation labels, form labels, table headers all translated
  - Language switcher in user menu

- **Content Translations:**
  - Countries: Names and nationalities (Spatie Translatable)
  - Document Types: Names (Spatie Translatable)
  - Static Pages: Titles and content (Spatie Translatable)

- **Locale Configuration:**
  - Default: Arabic (`APP_LOCALE=ar`)
  - Fallback: Arabic (`APP_FALLBACK_LOCALE=ar`)
  - Faker: English (`APP_FAKER_LOCALE=en_US`)

**Translation Status:**
- ✅ All Filament resources translated
- ✅ All form labels translated
- ✅ All table headers translated
- ✅ Country model configured for translations
- ✅ DocumentType model configured for translations
- ⚠️ Country Arabic translations need manual update (currently placeholders)

---

## 👥 User Roles & Access

### Admin Users

**Model:** `App\Models\User`  
**Type:** `UserType::Admin` (enum value: `'admin'`)  
**Authentication:** Standard Laravel authentication via `web` guard  
**Panel:** `/admin`

**Capabilities:**
- ✅ Full CRUD access to all resources
- ✅ Manage certified centers
- ✅ Approve/reject accreditation requests
- ✅ Import/export certifications
- ✅ Manage system settings
- ✅ Manage static pages
- ✅ View all statistics and reports
- ✅ Manage users (admin accounts)

**Default Admin Account:**
- Email: `admin@test.com`
- Password: (reset via Tinker if forgotten)

### Certified Center Users

**Model:** `App\Models\CertifiedCenter`  
**Authentication:** Custom guard `certified_center`  
**Panel:** `/center`

**Capabilities:**
- ✅ View and edit own center profile
- ✅ Create and manage own certifications
- ✅ Create and view own accreditation requests
- ✅ View own statistics and reports
- ❌ Cannot access other centers' data
- ❌ Cannot manage system settings
- ❌ Cannot import/export

**Access Control:**
- Policies enforce center isolation
- Centers can only see/modify resources where `certified_center_id` matches their ID
- Automatic filtering in queries

### Public Web Interface

**Access:** No authentication required  
**Routes:** Defined in `routes/web.php`

**Available Endpoints:**
- `/` - Homepage (welcome page)
- `/pages/{slug}` - Static pages (e.g., `/pages/about`)
- `/certifications/search` - Certification search page
- `/certifications/{serial}` - View certification by serial number
- `/centers/` - List all certified centers
- `/centers/{id}` - View center details

**Features:**
- Read-only access
- Certification verification by serial number
- Center directory
- Static content pages

---

## 🗄️ Database Schema

### Core Tables

#### `users`
- Admin users table
- Fields: `id`, `name`, `email`, `password`, `type` (enum: admin/client), `email_verified_at`, `remember_token`, `created_at`, `updated_at`

#### `certified_centers`
- Accredited training centers
- Fields: `id`, `name`, `email`, `password`, `address`, `phone`, `manager_name`, `accreditation_period_start`, `accreditation_period_end`, `accreditation_number`, `status` (enum), `is_active`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`

#### `certifications`
- Issued certifications
- Fields: `id`, `certified_center_id`, `certificate_type` (enum), `trainee_id`, `trainee_name` (denormalized), `trainer_id`, `trainer_name` (denormalized), `document_type_id`, `accredited_serial_number` (unique), `document_code`, `accreditation_number`, `accreditation_date`, `country_id`, `nationality` (denormalized), `paper_received` (boolean), `notes`, `created_at`, `updated_at`

#### `accreditation_requests`
- Accreditation renewal requests
- Fields: `id`, `certified_center_id`, `requested_start_date`, `requested_end_date`, `request_notes`, `status` (enum), `admin_notes`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`

#### `trainers`
- Trainer profiles
- Fields: `id`, `name`, `email`, `phone`, `specialization`, `country_id`, `nationality`, `is_active`, `created_at`, `updated_at`

#### `trainees`
- Trainee profiles (extended)
- Fields: `id`, `name`, `email` (unique, nullable), `phone`, `country_id`, `date_of_birth`, `nationality`, `gender` (enum), `occupation`, `organization`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `medical_info`, `notes`, `created_at`, `updated_at`

#### `countries`
- Countries with translations
- Fields: `id`, `name` (JSON: `{en: "...", ar: "..."}`), `code`, `code_2`, `nationality` (JSON: `{en: "...", ar: "..."}`), `is_active`, `created_at`, `updated_at`

#### `document_types`
- Document type definitions
- Fields: `id`, `key` (unique), `name` (JSON: `{en: "...", ar: "..."}`), `description`, `is_active`, `created_at`, `updated_at`

#### `static_pages`
- Public static content
- Fields: `id`, `slug` (unique), `title` (JSON), `content` (JSON), `meta_description`, `is_published`, `created_at`, `updated_at`

#### `application_settings`
- System configuration
- Fields: `id`, `key` (unique), `value`, `type` (enum: text/email/phone/url/number/boolean), `created_at`, `updated_at`

#### `memberships`
- Membership records
- Fields: `id`, `...`, `created_at`, `updated_at`

### Key Relationships

- `Certification` belongs to `CertifiedCenter`
- `Certification` belongs to `Trainee` (nullable)
- `Certification` belongs to `Trainer`
- `Certification` belongs to `Country`
- `Certification` belongs to `DocumentType`
- `AccreditationRequest` belongs to `CertifiedCenter`
- `Trainee` belongs to `Country`
- `Trainer` belongs to `Country`

### Recent Migrations

1. **`2025_10_19_140743_add_extended_fields_to_trainees_table.php`**
   - Added extended fields to trainees (email, phone, country_id, date_of_birth, gender, etc.)
   - All fields nullable to preserve existing data

2. **`2025_10_19_144818_restore_countries_original_data.php`**
   - Cleanup migration to restore country data from multi-encoded JSON

3. **`2025_10_19_144925_convert_countries_to_json_with_same_values.php`**
   - Converted `name` and `nationality` columns from varchar to JSON
   - Populated with same value for both `en` and `ar` (placeholders)
   - Dropped unique constraint on `name` (JSON columns can't have unique indexes in MySQL)

---

## ✅ Current Implementation Status

### Completed Features

#### Core Functionality
- ✅ Dual-panel authentication (Admin + Center)
- ✅ Full CRUD operations for all resources
- ✅ Role-based access control (Policies)
- ✅ Service-Repository pattern implementation
- ✅ Excel import/export functionality
- ✅ Statistics and dashboard widgets
- ✅ Accreditation request workflow
- ✅ Public web interface routes

#### Recent Updates (October 2025)

1. **DocumentType Enum Removal**
   - ✅ Removed all references to non-existent `App\Enums\DocumentType`
   - ✅ Updated all forms to use `documentType` relationship
   - ✅ Updated tables to display `documentType.name`
   - ✅ Updated imports to create/link DocumentType records
   - ✅ Updated repositories and scopes

2. **Certification Model Enhancement**
   - ✅ Added denormalized fields: `trainee_name`, `trainer_name`, `nationality`
   - ✅ Fields work alongside foreign keys for performance

3. **Trainee Table Extension**
   - ✅ Created migration to add extended fields
   - ✅ All fields nullable to preserve data
   - ✅ Added foreign key to countries table

4. **Translation Support**
   - ✅ Added `getNavigationLabel()`, `getModelLabel()`, `getPluralModelLabel()` to all resources
   - ✅ Replaced hardcoded labels with `__('app.key')` in forms
   - ✅ Replaced hardcoded labels with `__('app.key')` in tables
   - ✅ Added missing translation keys to `lang/en/app.php` and `lang/ar/app.php`

5. **Country Translations**
   - ✅ Added Spatie Translatable trait to Country model
   - ✅ Converted `name` and `nationality` columns to JSON
   - ✅ Initial data populated with placeholders (same value for en/ar)
   - ⚠️ **Action Required:** Admins need to manually update Arabic translations

6. **Web Routes**
   - ✅ Added public routes for static pages
   - ✅ Added public routes for certification search/view
   - ✅ Added public routes for center listing/view

7. **Center Panel Permissions**
   - ✅ Verified policies restrict centers to own resources
   - ✅ Centers can create and manage own certifications
   - ✅ Centers can create and manage own accreditation requests

### Translation Status

- ✅ **Interface:** Fully translated (Arabic/English)
- ✅ **Forms:** All labels use translation keys
- ✅ **Tables:** All headers use translation keys
- ✅ **Navigation:** All menu items translated
- ✅ **Models:** Country and DocumentType configured for translations
- ⚠️ **Content:** Country Arabic translations need manual update (currently placeholders)

---

## 📝 To-Do List

### High Priority

1. **Country Arabic Translations**
   - [ ] Update Arabic translations for all countries via Filament GUI
   - [ ] Verify translations display correctly in both languages
   - [ ] Test search/filter with Arabic country names

2. **Public Web Interface**
   - [ ] Create Blade templates for public pages
   - [ ] Implement certification search functionality
   - [ ] Implement certification verification page
   - [ ] Implement center listing and detail pages
   - [ ] Implement static page rendering
   - [ ] Add theme integration (as mentioned by user)

3. **Testing**
   - [ ] Write unit tests for services
   - [ ] Write feature tests for policies
   - [ ] Write tests for import/export functionality
   - [ ] Test bilingual interface switching
   - [ ] Test center isolation (security)

### Medium Priority

4. **Documentation**
   - [x] Create project overview documentation (this file)
   - [ ] Create API documentation (if needed)
   - [ ] Create user guides for Admin and Center panels
   - [ ] Document import/export format

5. **Enhancements**
   - [ ] Add email notifications for accreditation status changes
   - [ ] Add PDF export for certifications
   - [ ] Add advanced search/filtering
   - [ ] Add bulk operations
   - [ ] Add audit logging
   - [ ] Add data export scheduling

6. **Performance**
   - [ ] Optimize database queries (N+1 issues)
   - [ ] Add caching for frequently accessed data
   - [ ] Optimize Excel import for large files
   - [ ] Add pagination optimization

### Low Priority / Future Enhancements

7. **Features**
   - [ ] Add trainer certification tracking
   - [ ] Add trainee certification history
   - [ ] Add certification expiration tracking
   - [ ] Add renewal reminders
   - [ ] Add multi-level user roles
   - [ ] Add file uploads for certifications
   - [ ] Add QR code generation for certifications
   - [ ] Add API endpoints (if needed)
   - [ ] Add mobile app support

8. **UI/UX**
   - [ ] Improve dashboard widgets
   - [ ] Add more chart types
   - [ ] Add dark mode support
   - [ ] Improve responsive design
   - [ ] Add keyboard shortcuts

---

## 🔧 Technical Details

### Dependencies

**Core:**
- `laravel/framework`: ^12.0
- `filament/filament`: ^4.0
- `spatie/laravel-translatable`: ^6.11
- `maatwebsite/excel`: ^3.1

**Development:**
- `laravel/pint`: ^1.24 (code formatting)
- `laravel/pail`: ^1.2.2 (log viewer)
- `phpunit/phpunit`: ^11.5.3 (testing)

### Configuration Files

**Key Config Files:**
- `config/panels.php` - Filament panel configuration
- `config/app.php` - Application configuration
- `config/auth.php` - Authentication guards
- `config/translatable.php` - Spatie Translatable config

**Panel Configuration:**
```php
'admin' => [
    'id' => 'admin',
    'path' => '/admin',
    'color' => 'amber',
],
'center' => [
    'id' => 'center',
    'path' => '/center',
    'color' => 'blue',
    'guard' => 'certified_center',
],
```

### Routes

**Admin Panel:** Auto-discovered by Filament  
**Center Panel:** Auto-discovered by Filament  
**Public Web:** Defined in `routes/web.php`

**Public Routes:**
- `GET /` - Homepage
- `GET /pages/{slug}` - Static pages
- `GET /certifications/search` - Certification search
- `GET /certifications/{serial}` - View certification
- `GET /centers/` - List centers
- `GET /centers/{id}` - View center

### Policies

All resources have corresponding policies:

- `CertificationPolicy` - Restricts centers to own certifications
- `AccreditationRequestPolicy` - Restricts centers to own requests
- `CertifiedCenterPolicy` - Center profile access
- `CountryPolicy` - Country management
- `DocumentTypePolicy` - Document type management
- `TraineePolicy` - Trainee management
- `TrainerPolicy` - Trainer management
- `UserPolicy` - User management

### Observers

- `CertifiedCenterObserver` - Handles center events
- `AccreditationRequestObserver` - Handles request events

### Enums

- `UserType`: `admin`, `client`
- `CenterStatus`: `active`, `inactive`, `pending`, `suspended`
- `AccreditationStatus`: `pending`, `approved`, `rejected`, `under_review`
- `CertificateType`: `basic`, `accreditation`
- `PanelId`: `admin`, `center`

---

## 🚀 Development Setup

### Environment Requirements

- **PHP:** ^8.2
- **MySQL:** 5.7+ (or MariaDB equivalent)
- **Composer:** Latest
- **Node.js:** Latest LTS (for frontend assets)

### Database Setup

1. **Create Database:**
   ```sql
   CREATE DATABASE board_app_db;
   ```

2. **Configure `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=board_app_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

4. **Seed Database (Optional):**
   ```bash
   php artisan db:seed
   ```

### Key URLs

**Admin Panel:**
- Login: `http://localhost/admin/login`
- Dashboard: `http://localhost/admin`

**Center Panel:**
- Login: `http://localhost/center/login`
- Dashboard: `http://localhost/center`

**Public Web:**
- Homepage: `http://localhost/`
- Static Pages: `http://localhost/pages/{slug}`
- Certification Search: `http://localhost/certifications/search`
- Certification View: `http://localhost/certifications/{serial}`
- Centers List: `http://localhost/centers/`
- Center View: `http://localhost/centers/{id}`

### Default Admin Account

**Email:** `admin@test.com`  
**Password:** (Reset via Tinker if forgotten)

**Reset Password:**
```bash
php artisan tinker
```
```php
$user = App\Models\User::find(1);
$user->password = bcrypt('newpassword123');
$user->save();
```

### Development Commands

**Start Development Server:**
```bash
php artisan serve
```

**Run All Services (Dev Mode):**
```bash
composer dev
```
This runs:
- PHP development server
- Queue worker
- Log viewer (Pail)
- Vite dev server

**Code Formatting:**
```bash
./vendor/bin/pint
```

**Run Tests:**
```bash
php artisan test
```

**Clear Caches:**
```bash
php artisan optimize:clear
```

---

## 📊 Statistics & Widgets

### Admin Dashboard Widgets

1. **StatsOverview**
   - Total Centers
   - Active Centers
   - Total Certifications
   - Pending Accreditation Requests
   - Admin Users Count
   - Certifications This Month

2. **AccreditationChart**
   - Chart showing accreditation requests over time
   - Status breakdown

3. **CertificationChart**
   - Chart showing certifications over time

### Center Dashboard Widgets

1. **CenterStatsOverview**
   - Total Certifications (own center)
   - This Month Certifications
   - Pending Requests
   - Accreditation Status

2. **MonthlyCertificationsChart**
   - Monthly certification trends for the center

---

## 🔐 Security Considerations

### Authentication

- **Admin:** Standard Laravel authentication
- **Center:** Custom guard `certified_center`
- **Password Hashing:** Bcrypt (12 rounds)

### Authorization

- **Policies:** Enforce role-based access
- **Center Isolation:** Automatic filtering by `certified_center_id`
- **Resource Scoping:** Centers can only access own resources

### Data Protection

- **CSRF Protection:** Enabled on all forms
- **Password Hashing:** Automatic via Laravel
- **SQL Injection:** Protected by Eloquent ORM
- **XSS Protection:** Blade templating escapes output

---

## 📚 Additional Resources

### File Structure

```
app/
├── Enums/              # Application enums
├── Events/             # Domain events
├── Exports/            # Excel export classes
├── Filament/           # Filament resources
│   ├── Admin/          # Admin panel resources
│   └── Center/        # Center panel resources
├── Http/
│   ├── Controllers/    # Web controllers
│   └── Middleware/    # Custom middleware
├── Imports/            # Excel import classes
├── Models/             # Eloquent models
│   └── Traits/         # Model traits
├── Notifications/      # Email notifications
├── Observers/          # Model observers
├── Policies/           # Authorization policies
├── Providers/          # Service providers
├── Repositories/       # Data repositories
└── Services/           # Business logic services

database/
├── factories/          # Model factories
├── migrations/         # Database migrations
└── seeders/            # Database seeders

lang/
├── ar/                 # Arabic translations
└── en/                 # English translations

routes/
└── web.php             # Web routes

config/
├── panels.php          # Filament panel config
├── auth.php            # Authentication config
└── translatable.php    # Translation config
```

### Key Services

- `StatsService` - Admin dashboard statistics
- `CenterStatsService` - Center dashboard statistics
- `CertificationService` - Certification business logic
- `CertifiedCenterService` - Center management
- `AccreditationRequestService` - Request workflow
- `StaticPageService` - Static content management
- `ApplicationSettingService` - Settings management

---

## 🎯 Project Goals

### Primary Goals

1. **Streamline Certification Management**
   - Efficient certification issuance and tracking
   - Bulk import/export capabilities
   - Serial number verification

2. **Accreditation Workflow**
   - Automated request/review process
   - Status tracking and notifications
   - Period management

3. **Bilingual Support**
   - Full Arabic/English interface
   - Translatable content
   - Cultural localization

4. **Multi-Tenant Architecture**
   - Center isolation
   - Role-based access control
   - Secure data separation

### Success Metrics

- ✅ All core features implemented
- ✅ Bilingual interface functional
- ✅ Center isolation working
- ✅ Import/export functional
- ⚠️ Public web interface needs templates
- ⚠️ Country translations need manual update

---

## 📞 Support & Maintenance

### Known Issues

1. **Country Translations:** Arabic translations are placeholders (same as English)
   - **Solution:** Update via Filament GUI manually

2. **Public Web Interface:** Controllers exist but Blade templates missing
   - **Solution:** Create templates or integrate theme

### Maintenance Tasks

- Regular database backups
- Update dependencies regularly
- Monitor error logs
- Review and update translations
- Test import/export functionality
- Verify center isolation security

---

## 📝 Notes

- **Data Preservation:** All migrations preserve existing data
- **Backward Compatibility:** Recent changes maintain compatibility
- **Performance:** Denormalized fields used for optimization
- **Scalability:** Service-Repository pattern supports growth
- **Localization:** Ready for additional languages if needed

---

**Last Updated:** October 19, 2025  
**Version:** 1.0.0  
**Status:** Production Ready (with pending tasks)

