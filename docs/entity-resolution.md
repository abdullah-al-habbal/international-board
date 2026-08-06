# Entity resolution for the certification importer

Architecture and rationale for how the importer turns messy CSV names into stable entity ids.

The full planning document lives at `PLAN.md` (repo root). This file is the maintained summary; see the other `docs/entity-resolution-*` files for the details per component.

## The answer

Not one of three isolated options — two of them belong together in the import path, the third is demoted out of it:

- **Normalisation** runs at import time as a strictly ordered ladder of *exact* lookups.
- **An alias dictionary** feeds that ladder; every confirmed merge writes the losing spelling as an alias, so a decision a human makes once is deterministic forever after.
- **Fuzzy matching** runs offline, produces suggestions only, and never changes a row without a human confirming it.

A vector database is the wrong tool. Semantic similarity is not identity: an embedding model happily says "Ali Hassan" and "Ali Hussain" are near-identical; they are usually two different people. MariaDB 11.8 ships a native `VECTOR` type with `VEC_DISTANCE_COSINE()`, so it *could* be done without new infrastructure — but the scores do not separate, for the same reason Levenshtein does not.

## Why fuzzy matching cannot decide anything — measured, not asserted

Scoring a real country list with the composite metric (`max` of Jaro-Winkler, Levenshtein ratio, token Jaccard):

| Pair | Score | Truth |
|---|---|---|
| `niger` ~ `nigeria` | 0.943 | different countries |
| `austria` ~ `australia` | 0.927 | different countries |
| `mali` ~ `malawi` | 0.922 | different countries |
| `ireland` ~ `iceland` | 0.914 | different countries |
| `libanon` → `lebanon` | 0.914 | **same — want to match** |
| `qater` → `qatar` | 0.907 | **same — want to match** |
| `zambia` ~ `gambia` | 0.889 | different countries |
| `allebanon` → `lebanon` | 0.831 | **same — want to match** |

The distributions do not merely overlap, they interleave. Any threshold low enough to catch `libanon` also merges Ireland into Iceland and Niger into Nigeria. There is no safe cut-off, and no amount of algorithm-swapping produces one, because the information needed to separate the cases is not in the strings.

Names are worse than countries. Source data has no passport, national id, email or date of birth — a name is the only identifier available. `Ali Hassan` and `Ali Hussain` are one edit apart. Merging two real trainees is not a tidy-up; it is one person's certificate appearing under another person's record. The rule is therefore:

> **Deterministic transformations may merge; probabilistic ones may only suggest.**

This asymmetry is encoded as a regression test (`similarity_scores_of_same_and_different_entities_overlap`) so a future proposal for an auto-merge threshold has to explain itself against the measurements.

## The resolution ladder

Each rung is an exact lookup. Order is load-bearing.

```
1. exact key            "Syria"       → key "syria"          → hit
2. alias                "سوريا"        → alias key            → hit
3. noise tokens removed "like Syria"  → strip "like"          → "syria" → hit
4. leading article      "allebanon"   → strip "al"            → "lebanon" → hit
5. create + quarantine  anything else → provisional row + review queue entry
```

Rung 3 is the interesting one: `like Syria` is not a fuzzy problem. It is a known junk word next to a clean value, and removing a curated list of junk words is exact and reversible. `South Sudan` survives untouched, because `south` is not on the list — exactly the false positive a naive substring-containment check would have produced.

Rung 4 looks dangerous and is not, purely because of ordering. `Algeria` resolves on rung 1 and never reaches rung 4, so it is never mangled into `geria`. And when `geria` *is* generated for some other input, it is looked up exactly and misses, so it is discarded.

## Three defects the rewrite fixes

**The cache is not a deduplicator.** A per-process array cannot deduplicate across parallel workers — two chunk jobs that both meet a new trainee both insert. The unique index on `name_key` plus `upsert()` turns the loser of the race into a no-op update that reads back the winner's id. The array is an optimisation, not a correctness mechanism.

**`warmUp()` was a full table scan per chunk job.** Two hundred chunks meant two hundred full scans of a table that grows every import. Replaced with batch priming: collect the distinct names a batch mentions and resolve them in two indexed `whereIn` queries. Cost is proportional to the batch, not the table.

**`ResolveCountryHandler` inserted a country for anything it could not match.** This is how `allebanon` and `like Syria` became country rows. Auto-creation stays so imports never stall, but the new row is written with `review_status = 'provisional'` and the raw value is filed in `import_unresolved_values` with ranked suggestions. The result is labelled as a question instead of silently becoming a fact.

## Rollout order

Migration 3 refuses to run if step 2 is skipped, by design.

```bash
php artisan migrate                       # migrations 1 & 2: columns, aliases, review tables
php artisan entities:backfill-keys        # compute keys; seeds aliases from JSON name variants;
                                          # prints every key collision you must resolve
php artisan entities:merge --auto-exact --dry-run
php artisan entities:merge --auto-exact   # merge byte-identical keys only — not a guess
php artisan migrate                       # migration 3: the UNIQUE indexes
```

Ongoing:

```bash
php artisan entities:find-duplicates --entity=trainees --floor=0.88   # nightly; suggestions only
php artisan entities:merge --review                                   # human confirms
php artisan entities:merge --alias --entity=countries --id=12 --spelling="like Syria"
```

`--auto-exact` is safe unattended because byte-identical `name_key` values are rows the normaliser has already decided are one entity. `--review` is the only path that acts on a fuzzy signal, and it always requires a person.

## Known trade-offs

- **Identity is `name_key` alone, not `(name_key, country_id)`.** Country is frequently blank, and MariaDB does not collide `NULL`s in a unique index, so scoping by country would let the same person land twice — once with a country and once without. The cost: two genuinely different people with identical names become one record. The review queue catches it. If a passport or national ID column is added later, it becomes the primary identity key and this problem shrinks to a footnote; the resolver is structured so that change is additive.
- **`upsert()` burns auto-increment ids** on no-op updates, so id sequences show gaps. Harmless, but expected.
- **Aggressive whitespace removal in `name_key`** means `Ali Reza` and `Alir Eza` collide. Vanishingly rare; the alternative — keeping whitespace — fails the requirement that `abdullah alhabal` and `abdullah al habal` be one person.

## Component index

| Doc | Covers |
|---|---|
| `docs/name-normalization.md` | `NameNormalizer` — the three keys and the pipeline |
| `docs/fuzzy-matching.md` | `FuzzyMatcher` — scoring, ranking-only contract |
| `docs/entity-resolution-schema.md` | The three migrations |
| `docs/import-pipeline.md` | `CertificationImportService` + `ResolvesEntities` |
| `docs/entity-merging.md` | `EntityMerger` + alias learning loop |
| `docs/duplicate-finder.md` | `MatchCandidateFinder` blocking |
| `docs/entity-resolution-cli.md` | The three artisan commands |
| `docs/import-jobs.md` | Chunked queue pipeline |
