# Phase 2: Delete Obsolete Files

## Title

Remove Old Migrations, Models, and Filament Resources

## Description

Delete all files that are replaced by the consolidated migrations and new architecture. This includes: old incremental migration files, data-only migrations, the two request models (`CenterDocumentTypeRequest`, `TrainerDocumentTypeRequest`), and their corresponding Filament resource directories.

## Vision

A clean filesystem with no dead code. Only the 27 migration files (11 consolidated + 16 kept as-is), current models, and updated Filament resources remain.

## Tips

- **Do not** delete the old `certified_center_document_type` (singular) table migration — wait, it IS being deleted. It's a pivot table no longer needed.
- The `2026_04_11_210331_create_trainer_document_types_table.php` migration is also being deleted — its table is recreated by our Phase 1 consolidated migration.
- Use `git rm` or `rm` — doesn't matter, but verify deletions with `git status` afterward.
- The route cache at `bootstrap/cache/routes-v7.php` references deleted Filament classes — it will be regenerated in Phase 7.
- Do NOT delete `TrainerDocumentType` or `CertifiedCenterDocumentType` model files — they will be **updated** in Phases 3 and 5.

## Steps

### Step 1: Delete old migration files (32 files)

```bash
# Data-only / legacy fix migrations (no schema needed fresh)
rm database/migrations/2026_02_04_120000_sync_existing_trainee_names_to_trainee_id.php
rm database/migrations/2026_02_04_120001_drop_trainee_name_from_certifications_table.php
rm database/migrations/2026_02_13_212147_create_default_certificate_document_types.php
rm database/migrations/2026_02_13_212158_migrate_certificate_type_to_document_type_id.php
rm database/migrations/2026_02_13_212206_drop_certificate_type_column.php
rm database/migrations/2025_10_19_144818_restore_countries_original_data.php
rm database/migrations/2025_10_19_144925_convert_countries_to_json_with_same_values.php
rm database/migrations/2026_06_07_000000_make_accreditation_number_required_in_certified_centers.php

# Schema-altering migrations replaced by consolidated create files
rm database/migrations/2025_09_09_113329_add_missing_columns_to_certifications_table.php
rm database/migrations/2025_12_21_163026_remove_name_fields_from_certifications_table.php
rm database/migrations/2026_06_06_000000_make_document_code_not_null_in_certifications_table.php
rm database/migrations/2025_12_21_114618_add_country_id_to_certified_centers_table.php
rm database/migrations/2025_10_19_140743_add_extended_fields_to_trainees_table.php
rm database/migrations/2026_04_11_210247_make_country_codes_nullable.php
rm database/migrations/2026_04_11_210320_add_review_fields_to_accreditation_requests.php
rm database/migrations/2026_06_07_000001_add_under_review_to_accreditation_requests_status.php
rm database/migrations/2026_06_07_161425_add_accreditation_request_status_indexes.php
rm database/migrations/2026_05_30_160911_change_value_column_to_text_in_application_settings_table.php
rm database/migrations/2026_04_11_210318_add_is_published_to_certified_center_document_types.php

# Old create files replaced by consolidated versions
rm database/migrations/2025_09_09_113321_create_application_settings_table.php
rm database/migrations/2025_09_09_113324_create_accreditation_requests_table.php
rm database/migrations/2025_09_09_113322_create_countries_table.php
rm database/migrations/2025_09_09_113323_create_certified_centers_table.php
rm database/migrations/2025_09_09_113324_create_accreditation_requests_table.php
rm database/migrations/2025_09_09_113325_create_trainees_table.php
rm database/migrations/2025_09_09_113326_create_document_types_table.php
rm database/migrations/2025_09_09_113327_create_certifications_table.php

# Old trainer_accreditation_requests — now consolidated into Phase 1 Step 12
rm database/migrations/2026_04_11_223453_create_trainer_accreditation_requests_table.php

# Old document type tables replaced by new three-table design
rm database/migrations/2025_12_21_114624_create_certified_center_document_type_table.php
rm database/migrations/2026_02_25_094257_create_certified_center_document_types.php
rm database/migrations/2026_02_25_094352_create_center_document_type_requests.php
rm database/migrations/2026_04_11_210331_create_trainer_document_types_table.php
rm database/migrations/2026_04_11_210333_create_trainer_document_type_requests.php
```

### Step 2: Delete obsolete model, policy, and observer files

```bash
rm app/Models/CenterDocumentTypeRequest.php
rm app/Models/TrainerDocumentTypeRequest.php
rm app/Models/AccreditationRequest.php
rm app/Policies/AccreditationRequestPolicy.php
rm app/Observers/AccreditationRequestObserver.php
```

### Step 3: Delete obsolete Filament resource directories

```bash
rm -rf app/Filament/Admin/Resources/CenterDocumentTypeRequests
rm -rf app/Filament/Admin/Resources/TrainerDocumentTypeRequests
rm -rf app/Filament/Center/Resources/CenterDocumentTypeRequests
rm -rf app/Filament/Trainer/Resources/TrainerDocumentTypeRequests
```

### Step 4: Delete old relation manager

```bash
rm -rf app/Filament/Admin/Resources/DocumentTypes/RelationManagers
```

## Acceptance Criteria

- [ ] 32 old migration files deleted — only 24 remain in `database/migrations/`
- [ ] `CenterDocumentTypeRequest.php`, `TrainerDocumentTypeRequest.php`, and `AccreditationRequest.php` deleted from `app/Models/`
- [ ] `AccreditationRequestPolicy.php` deleted from `app/Policies/`
- [ ] `AccreditationRequestObserver.php` deleted from `app/Observers/`
- [ ] 4 Filament resource directories deleted from `Admin`, `Center`, `Trainer` panels
- [ ] `ApprovedCentersRelationManager.php` directory deleted
- [ ] `git status` shows only the intended deletions
