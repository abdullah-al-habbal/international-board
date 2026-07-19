# Decision: Explicit Accreditation Period Dates in Trainer Form

## Context
Previously, the `accreditation_period_start` was being set "under the hood" via `mutateFormDataBeforeCreate` in the `CreateTrainer` page class, while only the `accreditation_period_end` was visible in the Filament form. This lacked transparency and prevented admins from setting a custom start date (e.g., for backdated approvals or specific contract terms).

## Decision
Make both `accreditation_period_start` and `accreditation_period_end` explicit, visible, and required `DateTimePicker` fields in the `TrainerForm`.
- The start date defaults to `now()` for convenience but remains fully editable.
- The end date includes a `->after('accreditation_period_start')` validation rule to guarantee logical data integrity at the UI level.
- Removed the hidden `mutateFormDataBeforeCreate` mutation from `CreateTrainer` to ensure the form schema remains the single source of truth for data entry.

## Consequences
- Admins now have full visibility and control over the entire accreditation period.
- Data integrity is enforced by Filament's built-in date comparison validation.
- Code is cleaner and easier to maintain, as all form-related logic resides in the `TrainerForm` schema class.
