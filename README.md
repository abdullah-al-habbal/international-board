# Board - Certification Board Management System

A comprehensive web application built with Laravel 12 and Filament v4 for managing certification boards, accredited training centers, certifications, trainers, and trainees. The system facilitates the accreditation process, certification issuance, and provides public-facing interfaces for certification verification.

## 🚀 Quick Start

### Technology Stack
- **Framework:** Laravel 12.x
- **Admin Panel:** Filament v4.0
- **PHP:** 8.2+
- **Database:** MySQL
- **Default Locale:** Arabic (ar) with English (en) support

### Access Points

#### Admin Panel
- **URL:** `http://localhost/admin`
- **Guard:** `web` (default Laravel authentication)
- **Model:** `App\Models\User`
- **Access:** Full system administration

#### Center Panel
- **URL:** `http://localhost/center`
- **Guard:** `certified_center` (custom authentication)
- **Model:** `App\Models\CertifiedCenter`
- **Access:** Limited to own center resources

#### Public Routes
- **Homepage:** `http://localhost/`
- **Certification Search:** `http://localhost/certifications/search`
- **Centers Directory:** `http://localhost/centers/`

## 👥 Active Users

### Admin User
- **Email:** `admin@test.com`
- **Password:** `12345678`
- **Type:** Admin
- **Panel:** `/admin`

### Certified Center User
- **Email:** `center.one@gmail.com`
- **Password:** `12345678`
- **Name:** center one
- **Panel:** `/center`

## 📋 Core Features

- **Certification Management:** Create, edit, import/export certifications with serial number tracking
- **Center Management:** Manage accredited training centers with accreditation periods
- **Accreditation Workflow:** Request/review process with status tracking
- **Trainer & Trainee Management:** Comprehensive profiles and relationships
- **Bilingual Support:** Full Arabic/English interface with language switching
- **Excel Import/Export:** Bulk operations for certifications
- **Public Verification:** Search and verify certifications by serial number

## 🏗️ Architecture

### Dual-Panel System
The application uses Filament v4 with two separate authentication panels:
- **Admin Panel** (`/admin`) - Full system access
- **Center Panel** (`/center`) - Center-specific access with policy-based restrictions

### Service-Repository Pattern
- **Services:** Business logic layer (`app/Services/`)
- **Repositories:** Data access layer (`app/Repositories/`)
- **Policies:** Authorization layer (`app/Policies/`)

## 🔧 Configuration

Key configuration files:
- `config/panels.php` - Filament panel settings
- `config/auth.php` - Authentication guards (web, certified_center)
- `config/app.php` - Application settings
- `.env` - Environment variables

## 📝 Development

### Common Commands

```bash
# Check users
php artisan tinker
> \App\Models\User::all(['email', 'name'])

# Reset password
> $user = \App\Models\User::where('email', 'admin@test.com')->first();
> $user->password = \Illuminate\Support\Facades\Hash::make('new_password');
> $user->save();

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## 📚 Documentation

For detailed project documentation, see `docs/PROJECT_OVERVIEW.md`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
