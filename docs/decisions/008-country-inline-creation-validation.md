# Decision: Enforce Strict Validation on Inline Country Creation

## Context
When creating a new Trainer, Trainee, or Certification, the admin can inline-create a new `Country`. The forms previously allowed up to 10 characters for `code` and `code_2`. However, the database migration strictly defines `code` as `string(3)` and `code_2` as `string(2)`.
Entering a value longer than the column limit resulted in a `QueryException: Data too long for column`.

## Decision
Updated all forms with country inline creation to strictly enforce database limits at the UI validation stage:
- `code`: `maxLength(3)`, `minLength(3)`, `alpha()`, 
- `code_2`: `maxLength(2)`, `minLength(2)`, `alpha()`, 

Added `helperText` referencing ISO standards and  transformation for data uniformity.

### Files updated
- `TrainerForm.php` (Admin) — fixed broken `maxLength(10)` → correct ISO limits
- `TraineeForm.php` (Admin) — fixed broken `maxLength(10)` → correct ISO limits
- `TraineeForm.php` (Center) — fixed broken `maxLength(10)` → correct ISO limits
- `CertificationForm.php` (Admin) — added `alpha()`,  `minLength()`
- `CertificationForm.php` (Center) — added `alpha()`,  `minLength()`
- `CountryForm.php` (Admin) — added `alpha()`,  for consistency

## Consequences
- Prevents database-level truncation errors by catching invalid input at the Filament form validation stage.
- Ensures data consistency with ISO 3166-1 alpha-2 and alpha-3 standards.
- The form now acts as a reliable gatekeeper, matching the strictness of the underlying database schema.
