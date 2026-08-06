# Entity resolution CLI

Three artisan commands. They are the operational interface to the whole system.

## `entities:backfill-keys`

Populates `name_normalized` / `name_key` on existing rows, and seeds `entity_aliases` with every language variant found in the JSON name columns.

- Run after migration 1 and before migration 3 (the unique indexes).
- Prints the key collisions it finds — that is the list of merges you actually have to do before the unique index can be created.
- Safe to re-run. Re-run whenever `NameNormalizer`'s rules change — the keys are derived data, which is exactly why they are stored in a plain column rather than a generated one: changing the rules is a backfill, not an `ALTER TABLE` on a live table.

Options: `--table=` restrict to one table, `--chunk=` rows per batch, `--dry-run`.

For JSON name columns, `en` becomes the primary key and every other locale becomes an alias — which is how `سوريا` resolves to the same country as `Syria` without any cross-script transliteration guesswork.

## `entities:find-duplicates`

Populates the review queue. Schedule it nightly, or run it after a big import.

- Never changes an entity; everything it produces is a suggestion with `status = pending`.
- Options: `--entity=`, `--floor=` (minimum similarity, default 0.86), `--limit=`.
- Reports skipped oversized blocks explicitly, with a pointer to `MatchCandidateFinder::MAX_BLOCK_SIZE` if broader coverage is needed.

## `entities:merge`

The human end of entity resolution. Three modes, in ascending order of trust required:

### `--auto-exact`

Merges only rows whose `name_key` is byte-identical. These are not guesses: they are rows the normaliser already considers the same entity, which exist only because the unique index was not there yet. This is the pass that makes migration 3 applicable. Safe to run unattended.

### `--review`

Walks the pending fuzzy candidates one at a time and asks. Every confirmation writes aliases, so each decision is made once.

- "keep separate" is always the safe answer; it is recorded as `rejected` so the nightly scan never asks about that pair again.
- Options: `--entity=`, `--min-score=` (default 0.90).

### `--alias`

Teaches a spelling directly, without deleting anything:

```
php artisan entities:merge --alias --entity=countries --id=12 --spelling="like Syria"
```

Requires `--entity`, `--id` and `--spelling`; supports `--dry-run`.

All modes take `--dry-run` where applicable.
