# Entity resolution for the certification importer — revised plan

**Stack confirmed:** PHP 8.5.4 (local + Hostinger), MariaDB 11.8.6 local / 11.8.8 production, Laravel queue-driven chunked CSV import.

---

## The answer

Not one of the three. **Options 1 and 3 belong together in the import path; option 2 gets demoted out of it entirely.**

Concretely: normalisation (1) and an alias dictionary (3) run at import time as a strictly ordered ladder of *exact* lookups. Fuzzy matching (2) runs offline, produces suggestions only, and never changes a row without a human confirming it. The confirmation writes an alias, so every decision a person makes is made exactly once and becomes deterministic forever after. That feedback loop is the actual mechanism — it is what makes the system get better rather than merely get cleaned.

And a vector database is the wrong tool, but not for the reason you assumed. Embedding latency is a real cost, but the disqualifying problem is that semantic similarity is not identity. An embedding model will happily tell you "Ali Hassan" and "Ali Hussain" are near-identical; they are usually two different people. Note also that MariaDB 11.8 ships a native `VECTOR` type with `VEC_DISTANCE_COSINE()`, so you *could* do this without new infrastructure. You still should not, for the same reason you should not use Levenshtein: the scores do not separate.

## Why fuzzy matching cannot decide anything — measured, not asserted

This is the finding that settles the design. Scoring a real country list with the composite metric (`max` of Jaro-Winkler, Levenshtein ratio, token Jaccard):

| Pair | Score | Truth |
|---|---|---|
| `niger` ~ `nigeria` | **0.943** | different countries |
| `austria` ~ `australia` | **0.927** | different countries |
| `mali` ~ `malawi` | **0.922** | different countries |
| `ireland` ~ `iceland` | **0.914** | different countries |
| `libanon` → `lebanon` | 0.914 | **same — want to match** |
| `qater` → `qatar` | 0.907 | **same — want to match** |
| `zambia` ~ `gambia` | 0.889 | different countries |
| `allebanon` → `lebanon` | 0.831 | **same — want to match** |

The two distributions do not merely overlap, they interleave. Any threshold low enough to catch `libanon` also merges Ireland into Iceland and Niger into Nigeria. Drop it far enough to catch `allebanon` at 0.831 and you have merged most of the list. There is no cut-off, and no amount of algorithm-swapping produces one, because the information needed to separate the cases is not in the strings.

Names are worse than countries, not better. Your source data has no passport, national ID, email or date of birth — a name is the only identifier available. `Ali Hassan` and `Ali Hussain` are one edit apart. Merging two real trainees is not a tidy-up; it is one person's certificate appearing under another person's record, discovered months later by the person who was denied their own credential, and it is very hard to unpick once certifications have been repointed.

So the rule is: **deterministic transformations may merge; probabilistic ones may only suggest.**

This asymmetry is encoded as a regression test (`similarity_scores_of_same_and_different_entities_overlap`) so that if someone later proposes an auto-merge threshold, the test states why it was rejected.

## What the ladder actually catches

Each rung is an exact lookup. Order is load-bearing.

```
1. exact key            "Syria"       → key "syria"          → hit
2. alias                "سوريا"        → alias key            → hit
3. noise tokens removed "like Syria"  → strip "like"          → "syria" → hit
4. leading article      "allebanon"   → strip "al"            → "lebanon" → hit
5. create + quarantine  anything else → provisional row + review queue entry
```

Rung 3 is the interesting one: `like Syria` is not a fuzzy problem at all. It is a known junk word next to a clean value, and removing a curated list of junk words is exact and reversible. `South Sudan` survives it untouched, because `south` is not on the list — which is exactly the false positive a naive substring-containment check would have produced.

Rung 4 looks dangerous and is not, purely because of ordering. `Algeria` resolves on rung 1 and never reaches rung 4, so it is never mangled into `geria`. And when `geria` *is* generated for some other input, it is looked up exactly and misses, so it is discarded. Article stripping can only ever produce a hit against a name that genuinely exists.

## Three real defects in the current code

**The cache is not a deduplicator.** `$this->cache` lives in one PHP process. Two chunk jobs that both encounter a new trainee both miss, both insert, and you get two trainees — the exact outcome the cache was added to prevent. Nothing in the schema prevents it either. The fix is a `UNIQUE` index on `name_key` plus `upsert()`, which turns the loser of the race into a no-op update and lets it read back the winner's id. The array becomes what it should always have been: an optimisation, not a correctness mechanism.

**`warmUp()` is a full table scan per chunk job.** Every `ImportCertificationChunkJob` calls `warmUpHandlers()`, which reads all of `trainees` into an array before processing 500 rows. Two hundred chunks means two hundred full scans and two hundred copies of the table in memory, and it degrades every time the table grows. Replaced with batch priming: collect the ~500 distinct names a batch mentions, resolve them in two indexed `whereIn` queries. Cost becomes proportional to the batch, not the table.

**`ResolveCountryHandler` inserts a country for anything it cannot match.** This is the direct cause of `allebanon` and `like Syria` existing as country rows — the importer was asked to guess and guessed "this is a new nation state." You chose to keep auto-creation so imports never stall, so it stays, but the row is now written with `review_status = 'provisional'` and the raw value is filed in `import_unresolved_values` with ranked suggestions attached. The behaviour is unchanged; the difference is that the result is now labelled as a question instead of silently becoming a fact.

## PHP 8.5

The pipe operator fits the normalisation pipeline precisely, because every step is genuinely unary:

```php
return $value
    |> trim(...)
    |> self::toNfkc(...)
    |> self::toLower(...)
    |> self::decompose(...)
    |> self::stripCombiningMarks(...)
    |> self::stripTatweel(...)
    |> self::foldArabicLetters(...)
    |> self::toAsciiDigits(...)
    |> self::punctuationToSpace(...)
    |> self::collapseWhitespace(...);
```

Three constraints to keep in mind. Every callable must accept exactly one *required* parameter — multi-argument functions need a closure wrapper, since PHP's pipe has no partial application. By-reference parameters are rejected (`explode('-', $s) |> array_pop(...)` is an error). And `void` returns coerce to `null`, so a logging step mid-chain silently destroys the value.

**Deployment caveat worth checking before you merge:** `|>` is a *parse* error on PHP < 8.5, not a runtime error, so a file using it fails to load entirely on an older interpreter. Confirm every path that executes this code is on 8.5 — production CLI *and* the FPM pool, which are not always the same build on Hostinger — and confirm your Pint and PHPStan versions can parse it, or `pint` will fail on these files.

## MariaDB 11.8

MariaDB has **no built-in `LEVENSHTEIN`** — it needs a compiled C UDF, which requires write access to the server plugin directory and is not available to you on shared hosting. Every similarity function here is therefore pure PHP, which is fine because none of it runs during an import. `SOUNDEX()` exists but is English-phonetic and performs poorly on transliterated Arabic, so it is unused.

Keys are stored in a plain indexed column rather than a `PERSISTENT` generated column, deliberately. Generated columns would work — `LOWER`/`REPLACE`/`REGEXP_REPLACE` are deterministic and permitted — but then the normalisation rules live in two places, and changing them means an `ALTER TABLE` on a live table instead of re-running a backfill command. `name_key` uses `utf8mb4_bin` so comparison is exact bytes; we have already done the normalising and do not want a collation making additional equality decisions on top.

One collation note if you want it: 11.8 defaults to utf8mb4 with UCA 14.0.0 collations, so `utf8mb4_uca1400_ai_ci` gives accent-insensitive comparison at the DB level. It is not required here (the normaliser handles it) but it is worth having on display columns. Verify availability with `SHOW COLLATION LIKE 'utf8mb4_uca1400%'` before using it.

## Rollout order

The order matters — migration 3 will refuse to run if you skip step 2, by design.

```bash
php artisan migrate                       # migrations 1 & 2: columns, aliases, review tables
php artisan entities:backfill-keys        # compute keys; seeds aliases from JSON name variants;
                                          # prints every key collision you must resolve
php artisan entities:merge --auto-exact --dry-run
php artisan entities:merge --auto-exact   # merge byte-identical keys only — not a guess
php artisan migrate                       # migration 3: the UNIQUE indexes
```

Then, ongoing:

```bash
php artisan entities:find-duplicates --entity=trainees --floor=0.88   # nightly; suggestions only
php artisan entities:merge --review                                   # human confirms
php artisan entities:merge --alias --entity=countries --id=12 --spelling="like Syria"
```

`--auto-exact` is safe to run unattended because byte-identical `name_key` values are rows the normaliser has already decided are one entity; they exist only because the unique index was not there yet. `--review` is the only path that acts on a fuzzy signal, and it always requires a person.

## The offline duplicate finder

Comparing each name against every other is O(n·m): at 100k trainees and 500k rows that is roughly 5×10¹⁰ comparisons, which is the real reason the loop-over-the-cache version of option 2 cannot ship. `MatchCandidateFinder` uses standard record-linkage **blocking** — only compare records that already share a cheap key — across three independent passes, because each recovers pairs the others miss:

The exact block key (token-sorted, article-stripped) catches reordering, so `Habal, Abdullah Al` and `Abdullah Al-Habal` land in the same block. A four-character prefix catches typos late in the string (`morocco`/`moroco`). The consonant skeleton — sorted unique consonants, treating `y`, `w` and `h` as vowels — catches vowel drift, which is the dominant error class in transliterated Arabic and is particularly apt given Semitic roots are consonantal: `lebanon`, `libanon` and `allebanon` all reduce to `bln`, and `Mohamed`, `Muhammad` and `Mohammed` all reduce to `dm`.

Blocks over 250 members are skipped rather than scored, and the command *reports* how many it skipped. A silent cap would read as "we compared everything" when it did not.

## Known trade-offs

Trainee identity is `name_key` alone, not `(name_key, country_id)`. Country is frequently blank in the source, and MariaDB does not collide `NULL`s in a unique index, so scoping by country would let the same person land twice — once with a country and once without. The cost is that two genuinely different people with identical names become one record. Given the data available that is unavoidable at import time; the review queue is where it gets caught. If you later add a passport or national ID column, that becomes the primary identity key and this whole problem shrinks to a footnote — the resolver is structured so that change is additive.

`upsert()` burns auto-increment ids on no-op updates, so id sequences will show gaps. Harmless, but expected.

Aggressive whitespace removal in `name_key` means `Ali Reza` and `Alir Eza` collide. Vanishingly rare, and the alternative — not removing whitespace — fails your actual stated requirement that `abdullah alhabal` and `abdullah al habal` be one person.

## Files

| Path | Role |
|---|---|
| `app/Support/Text/NameNormalizer.php` | The single source of truth for all three keys. PHP 8.5 pipes. |
| `app/Services/Entity/FuzzyMatcher.php` | Jaro-Winkler, Levenshtein ratio, token Jaccard. Ranking only. |
| `app/Services/Entity/MatchCandidateFinder.php` | Blocked duplicate scan → review queue. |
| `app/Services/Entity/EntityMerger.php` | Transactional merge; writes aliases so the system learns. |
| `app/Services/Certification/Handlers/ResolvesEntities.php` | Batch resolution + the ladder + race-safe creation. |
| `app/Services/Certification/Handlers/Resolve*Handler.php` | Per-entity policy (open vs closed set, noise tokens). |
| `app/Services/Certification/CertificationImportService.php` | Batched two-phase import. |
| `app/Console/Commands/*` | `entities:backfill-keys`, `entities:find-duplicates`, `entities:merge`. |
| `database/migrations/*` | Columns → alias/review tables → unique indexes, in that order. |
| `tests/Unit/NameNormalizerTest.php` | The specification, including the anti-auto-merge guard. |

## Verification performed

The pipe chains were mechanically rewritten to nested calls and executed on PHP 8.4 — semantics confirmed as left-to-right, `normalize()` expanding to `collapseWhitespace(punctuationToSpace(toAsciiDigits(foldArabicLetters(stripTatweel(stripCombiningMarks(decompose(toLower(toNfkc(trim($value)))))))))`. All 26 assertions pass, including all eight `abdullah al habal` spellings collapsing to `abdullahalhabal`, four Arabic diacritic/tatweel variants collapsing to `عبداللهالحبال`, and the six must-not-merge pairs staying distinct. Every file passes `php -l`.

Three notes on that verification. The pipe *syntax* is verified against the RFC documentation, not by execution, since PHP 8.5 was not installable in the environment used — run the unit test on your 8.5.4 box before merging. And the `EntityMerger` reference map lists the foreign keys it knows about (`certifications.trainee_id`, `assigned_trainer_id`, `country_id`, `documentable_id`, `trainees.country_id`); if your schema has others pointing at these tables, add them, because a missing entry orphans rows rather than erroring. Finally, `importCertifications()` no longer calls `CsvImportHandler::import()` — that callback-per-row API cannot express batched resolution, so the file is read directly via `SplFileObject`, mirroring `ImportCertificationChunkJob`. Check whether anything else depends on the old code path.

## Sources

- [PHP 8.5: Pipe operator](https://php.watch/versions/8.5/pipe-operator) — semantics, unary constraint, by-reference and void limitations
- [PHP 8.5 feature list](https://php.watch/versions/8.5)
- [MariaDB 11.8 LTS release notes](https://mariadb.org/11-8-lts-released/) — vector type, utf8mb4/UCA 14.0.0 default
- [MariaDB generated columns](https://mariadb.com/docs/server/reference/sql-statements/data-definition/create/generated-columns) — determinism and index rules
- [Levenshtein MySQL/MariaDB UDF](https://github.com/juanmirocks/Levenshtein-MySQL-UDF) — confirms no built-in equivalent
- [Fuzzy string search for MySQL](https://villagesql.com/blog/fuzzy-string/) — trigram, edit-distance and phonetic trade-offs
- [Efficient record linkage: the critical role of blocking](https://www.mdpi.com/1999-4893/18/11/723)
- [Double Metaphone blocking for record linkage](https://link.springer.com/chapter/10.1007/978-981-95-0695-8_12)
- [Breaking Laravel's firstOrCreate using race conditions](https://freek.dev/1087-breaking-laravels-firstorcreate-using-race-conditions)
