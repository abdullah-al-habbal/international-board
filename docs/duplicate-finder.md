# Offline duplicate finder

`app/Services/Entity/MatchCandidateFinder.php` is the offline duplicate hunter. It runs from a scheduled command, never from an import, and never changes an entity — everything it produces is a suggestion in `entity_merge_candidates` with `status = pending`.

## Why blocking

Comparing each name against every other is O(n·m): at 100k trainees and 500k rows that is roughly 5×10¹⁰ comparisons, which is the reason a loop-over-the-cache duplicate scan cannot ship. Standard record-linkage fix: **blocking** — only compare records that already share a cheap key, so the quadratic term applies to small groups instead of the table.

## Three independent blocking passes

Each pass recovers pairs the others miss:

1. **Exact block key** (token-sorted, article-stripped) — catches reordering: `Habal, Abdullah Al` and `Abdullah Al-Habal` land in the same block.
2. **Four-character prefix** — catches typos late in the string: `morocco`/`moroco`.
3. **Consonant skeleton** (sorted unique consonants; Latin `y`, `w`, `h` treated as vowels) — catches vowel drift, the dominant error class in transliterated Arabic and particularly apt given Semitic roots are consonantal: `lebanon`, `libanon` and `allebanon` all reduce to `bln`; `Mohamed`, `Muhammad` and `Mohammed` all reduce to the same skeleton.

Blocks over 250 members are **skipped** rather than scored (a block of 3,000 names would cost 4.5M comparisons and is almost always a degenerate key, e.g. every single-word name). Skips are reported, never silent — a silent cap would read as "we compared everything" when it did not.

## Output

Pairs scoring at or above the floor are persisted to `entity_merge_candidates` via `upsert` keyed on `(entity_type, primary_id, duplicate_id)`. Only `score`/`strategy`/names are refreshed on re-scan — `status` is left alone, so a pair a human rejected is never resurrected.

`scan()` returns `['pairs', 'scanned', 'skipped_blocks']` so the CLI can report coverage limits explicitly.
