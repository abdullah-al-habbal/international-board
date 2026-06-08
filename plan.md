# Project Restructure Plan

## Objective

Prepare the codebase for a clean `migrate:fresh` by consolidating migrations to **one file per table**, removing `nationality` from the `certifications` table (sourced from `Country` relationship), and restructuring document types into **three independent tables** with embedded approval workflow.

## How to Use This Plan

Each phase is a numbered file in `docs/tasks/`. Process them **in order** — each phase depends on the previous one completing successfully.

| Phase | File | Description |
|-------|------|-------------|
| 1 | `01-migration-consolidation.md` | Create consolidated `create_` migration files (one per table) |
| 2 | `02-delete-obsolete-files.md` | Delete old migration files, model files, and Filament resource directories |
| 3 | `03-model-updates.md` | Update models: `DocumentType`, `CertifiedCenterDocumentType`, `TrainerDocumentType`, `Certification`, `CertifiedCenter`, `Trainer` |
| 4 | `04-nationality-removal.md` | Remove `nationality` from `Certification` model and all code references |
| 5 | `05-document-type-restructuring.md` | Rewrite doc type models with `key`/`name`/`status`/`reviewer`; remove FK references to `board_document_types` |
| 6 | `06-filament-resource-updates.md` | Update/create Filament resources for the three doc type tables with approval workflow |
| 7 | `07-verify-and-cleanup.md` | Run `migrate:fresh`, seed, clear caches, verify no broken routes |
| 8 | `08-behavioral-rules.md` | Redirects, profile editing, welcome widget, import source rules |

## Key Decisions

- **No cross-references** between `board_document_types`, `trainer_document_types`, and `certified_center_document_types`
- **`status` field** (`pending`/`approved`/`rejected`) replaces `is_published` and the old request table workflow
- **`nationality`** removed from `certifications` — access via `$certification->country?->nationality`
- **Countries** `name`/`nationality` stored as JSON for Spatie translatable
- **Certifications** FK `document_type_id` → `board_document_types`
- **`accreditation_requests`** renamed to **`center_accreditation_requests`** to match `trainer_accreditation_requests` pattern
- **Accreditation requests** no longer have user-specified dates — `requested_start_date`/`requested_end_date` removed; `accreditation_start_date`/`accreditation_end_date` set by admin on approval
- **`source` column** on `certifications` (enum: board/center/trainer) tracks origin; imports from Excel are always `source = 'board'`
- **Trainer membership** `membership_start_date` auto-set to `now()` on create; admin chooses `membership_end_date`

## Execution Order

```
Phase 1  →  create new migration files (incl. center_accreditation_requests, source column, accreditation dates)
Phase 2  →  delete old files (migrations, models, filament dirs)
Phase 3  →  update models (incl. model renames, accreditation dates, source enum, scopes)
Phase 4  →  remove nationality references
Phase 5  →  restructure doc type models
Phase 6  →  update Filament resources (incl. accreditation forms, trainer membership, redirects, welcome widget)
Phase 7  →  verify (migrate:fresh, seed, clear cache, test)
Phase 8  →  apply behavioral rules (redirects, profile editing, welcome message, import source)
```

> **Note:** The bootstrap route cache (`bootstrap/cache/routes-v7.php`) references deleted Filament classes. Phase 7 handles this with `route:clear`.
