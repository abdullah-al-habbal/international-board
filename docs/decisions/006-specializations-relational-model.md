# Decision: Relational Model for Trainer Specializations

## Context
Previously, `specializations` on the `Trainer` model was stored as a JSON array with hardcoded options in the Filament form. This made it impossible for Admins to add, edit, or deactivate specializations without deploying code changes, and it lacked referential integrity.

## Decision
Migrate `specializations` to a proper Many-to-Many relational database structure:
1. Created a `specializations` table (with a translatable `name` JSON column via `spatie/laravel-translatable` and `is_active` flag).
2. Created a `specialization_trainer` pivot table to link trainers and specializations.
3. Created a dedicated `SpecializationResource` in the Filament Admin panel for full CRUD management.
4. Updated the `Trainer` model to use `belongsToMany` and removed the legacy `array` cast.
5. Updated Filament Forms, Tables, and Infolists to use `->relationship('specializations', 'name')`.
6. Created a `SpecializationSeeder` to seed all 12 initial specializations with translatable names (en + ar).
7. Updated `TrainerSeeder` to attach random specializations to seeded trainers.

## Consequences
- Admins now have full, dynamic control over the list of available specializations.
- Data integrity is enforced via foreign key constraints with cascade deletes.
- The UI is cleaner, and the hardcoded array in `TrainerForm` has been eliminated.
- Center panel forms, tables, and infolists read from the relationship without any code changes needed for new specializations.
