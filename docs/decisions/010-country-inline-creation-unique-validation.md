# Decision: Unique Validation for Inline Country Creation

## Context
When creating a new Country inline within the Trainer, Trainee, or Certification forms, attempting to use an existing `code` or `code_2` (e.g., 'USA') resulted in a `UniqueConstraintViolationException` from the database.

## Decision
Apply Filament's `->unique()` validation rule to the `code` and `code_2` `TextInput` fields within the `createOptionForm` across all forms that support inline country creation:
- `TrainerForm.php` (Admin)
- `TraineeForm.php` (Admin)
- `TraineeForm.php` (Center)
- `CertificationForm.php` (Admin)
- `CertificationForm.php` (Center)

The `->unique(Country::class, 'column')` rule queries the database during form validation (on field blur or submit), providing immediate, user-friendly feedback if a country with that code already exists, preventing the database-level exception entirely.

## Consequences
- Prevents `UniqueConstraintViolationException` by catching duplicates at the application validation layer.
- Provides clear, live feedback to the user (e.g., "The code has already been taken").
- All forms with inline country creation are now consistent in validation behavior.
