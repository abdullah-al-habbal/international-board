# Entity resolution schema

Three migrations, applied in order. The last one refuses to run until the data is ready.

## Migration 1 — `2026_08_06_100000_add_resolution_keys_to_entity_tables.php`

Adds two resolution columns to every table the importer resolves against (`trainees`, `trainers`, `countries`, `board_document_types`):

- `name_normalized` — human-readable canonical form. Default collation is fine; this column is for display and fuzzy input, never for identity.
- `name_key` — identity key. `utf8mb4_bin` so comparison is exact bytes: the normalisation is already applied and a collation must not quietly decide two keys are equal on top of it. Indexed.

**No unique index is created here on purpose** — existing rows almost certainly contain duplicates. Run `entities:backfill-keys` and then `entities:merge` before applying the unique-index migration.

Also adds `review_status` (`confirmed` default, indexed) to the four tables. Auto-created rows are quarantined rather than blocked, so the import keeps running while a human decides whether they are real.

On SQLite (the test runner) the `utf8mb4_bin` collation is skipped — SQLite does not know it and its default collation is already binary.

## Migration 2 — `2026_08_06_100100_create_entity_resolution_tables.php`

The learning layer. Three tables:

**`entity_aliases`** — the dictionary the importer consults. Every confirmed merge writes the losing spellings in here, so a variant a human resolves once is resolved deterministically and instantly on every subsequent import.

- `aliasable_type` / `aliasable_id` — polymorphic owner.
- `alias_key` — output of `NameNormalizer::key()` for this spelling.
- `alias_label` — the raw spelling as first seen, kept for auditability.
- `source` — `manual | merge | seed | import`.
- `UNIQUE(aliasable_type, alias_key)` — one key can only ever point at one entity of a given type. This is what makes alias lookup an unambiguous, deterministic resolution step.

**`entity_merge_candidates`** — the review queue that fuzzy matching writes to. Nothing in it takes effect until a human confirms it.

- `entity_type`, `primary_id`, `duplicate_id`
- `score` — `0.0000`–`1.0000`
- `strategy` — which signal nominated the pair: `block | article | noise | fuzzy`
- `status` — `pending | merged | rejected`
- `UNIQUE(entity_type, primary_id, duplicate_id)` so the nightly scan never re-queues a pair that was decided.

**`import_unresolved_values`** — records every value the importer could not confidently resolve, so nobody has to grep logs to find out what the CSV actually contained.

- `entity_type`, `raw_value`, `normalized_value`
- `resolution` — what was done with it: `created | skipped`
- `created_entity_id`
- `suggestions` — ranked fuzzy suggestions, for the reviewer's convenience only (JSON)
- `status` — `pending`
- `UNIQUE(entity_type, raw_value)`.

## Migration 3 — `2026_08_06_100200_add_unique_name_key_indexes.php`

The hard guarantee. Adds `UNIQUE(name_key)` to all four tables.

The unique index is what makes concurrency safe. Two chunk jobs racing on the same new trainee can no longer both win: one insert succeeds, the other is ignored, and both jobs read back the same id. Without this index, an in-memory cache is not a deduplication mechanism at all — it is per-process and blind to sibling jobs.

`guardAgainstDuplicates()` fails loudly and usefully before adding the index rather than letting MariaDB emit a bare "Duplicate entry" that names one row and hides the other 200. The error lists a sample of the colliding keys and points at `entities:find-duplicates` + `entities:merge --auto-exact`.

Run ONLY after:

```bash
php artisan entities:backfill-keys
php artisan entities:find-duplicates
php artisan entities:merge --auto-exact
```
