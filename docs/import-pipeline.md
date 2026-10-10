# The import pipeline

Two layers: `CertificationImportService` drives the batch, and `ResolvesEntities` (base class of the four resolvers) does the actual resolution.

## CertificationImportService

The structural change from the previous version is that entity resolution is now **batched** rather than per-row.

**Before:** every chunk job called `warmUpHandlers()`, which read the whole `trainees` table into a PHP array before touching a single row. Two hundred chunk jobs meant two hundred full table scans, and the cost grew with every import. Worse, the array was per-process, so two jobs meeting the same new trainee both inserted one.

**Now:** rows are buffered in batches of 500. Each batch collects its distinct names, resolves them in a handful of indexed queries, then maps every row from an in-memory map. The unique index on `name_key` — not the array — is what guarantees a name maps to one id, so parallel workers are safe.

Query cost per 500-row batch is roughly constant: two SELECTs and at most one write per entity type, plus one upsert for the certifications themselves.

### Reading the file directly

`importCertifications()` reads the CSV directly via `SplFileObject` rather than going through `CsvImportHandler::import()`. That callback-per-row API hands rows over one at a time, which is precisely the shape that forced the old per-row lookups and cannot express batched resolution. `CsvImportHandler` stays injected for the rest of the class / other callers.

BOM and header whitespace are handled by `CsvHeaderMapper`, which also maps Arabic header spellings (`الجنسية`, `الحنسية`, `اسم المتدرب`, ...) to internal keys.

### Batch flow

1. Buffer up to 500 rows; for each, extract trimmed values and collect the distinct country / document-type / trainer names the batch mentions.
2. Resolve countries, document types and trainers in bulk (`resolveMany`).
3. Collect trainee names with first-non-null country as context, resolve trainees in bulk.
4. Build each row's insert payload. A row missing `trainee_name` (or whose trainee could not be resolved) fails per-row with `MissingValueException`; a row with more columns than the header fails with `RowLengthException` — both counted in `failed`, never aborting the batch.
5. `upsert()` the certification payload in one transaction, keyed on `accreditation_number` so re-imports are idempotent.

## ResolvesEntities

Base class for every "given a messy string, give me an id" resolver (`ResolveTraineeHandler`, `ResolveCountryHandler`, `ResolveTrainerHandler`, `ResolveDocumentTypeHandler`).

### Two principles

1. **Resolution is bulk, not per-row.** Each batch of ~500 rows issues exactly two SELECTs (main table + aliases) restricted to the keys that batch actually mentions, and at most one write statement. Cost is proportional to the batch, not to the table.
2. **The database is the deduplicator, not the array.** A per-process cache cannot deduplicate across parallel workers. The unique index on `name_key` is the real guarantee; `upsert()` makes the loser of the race read back the winner's id instead of throwing. The cache only avoids repeating work.

A cache guard (`MAX_CACHED_KEYS = 50_000`) protects a long-lived queue worker from unbounded growth; resolution stays correct when the cache is dropped — it just costs one more SELECT.

### The resolution ladder

Strictly ordered, every rung EXACT:

```
exact key → alias → noise-token-stripped → article-stripped → create
```

Fuzzy matching never appears in it. When a derived key hits, the spelling is remembered as an alias (`rememberAlias`) so the next import resolves it on rung 1.

### Closed vs open sets

- `isClosedSet()` (`true` for countries and document types): the vocabulary is small and curated, so it is worth loading once in full — every miss is interesting. `warmUp()` is a no-op guard for open sets so a stray call on trainees cannot quietly reintroduce the full-table-scan problem.
- Open sets (trainees, trainers): never preloaded; batch priming only.

### Creating missing rows

`createMissing()` inserts everything a batch is missing in one statement, then reads ids back in one more. `upsert()` rather than `insert()`: if a sibling worker inserted the same key a millisecond ago, the unique index turns the row into a no-op update instead of an exception, and the follow-up SELECT hands back their id. That is the whole concurrency story — no locks, no retries, no duplicate rows.

### Quarantine, not silent creation

For closed sets, an unmatched value that gets created is filed in `import_unresolved_values` with ranked fuzzy suggestions (`reportUnresolved`), so a human can turn it into an alias later. A new trainee is normal; a new country almost never is.

### Per-entity policy

| Handler | Table | Set | Notes |
|---|---|---|---|
| `ResolveTraineeHandler` | `trainees` | open | Never fuzzy match; identity is `name_key` alone (see trade-offs). New rows `review_status = 'confirmed'`. |
| `ResolveTrainerHandler` | `trainers` | open | Accreditation number generated per row inside `newEntityAttributes()` and cannot be part of the identity key. |
| `ResolveCountryHandler` | `countries` | closed | Noise tokens (`like`, `approx`, `unknown`, ...). New rows `review_status = 'provisional'` + filed unresolved. Suggestions ranked from non-provisional countries. |
| `ResolveDocumentTypeHandler` | `board_document_types` | closed | Noise tokens (`the`, `a`, `of`, `type`, ...). New rows provisional with a generated `key`. |

### Missing-value semantics

`handle()` single-value wrappers remain for backwards compatibility. `ResolveTraineeHandler::handle()` and `ResolveDocumentTypeHandler::handle()` throw `MissingValueException` when resolution fails; the others return `?int`.
