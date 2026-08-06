# Fuzzy matching — ranking only, never deciding

`app/Services/Entity/FuzzyMatcher.php` exists to RANK candidates for a human, not to decide anything.

## The measurements that rule out auto-merge

Against a real country list, the score distributions of "same entity" and "different entity" pairs overlap completely:

```
    libanon   -> lebanon    0.914   SAME       (want to match)
    ireland   -> iceland    0.914   DIFFERENT  (must not match)
    qater     -> qatar      0.907   SAME       (want to match)
    austria   -> australia  0.927   DIFFERENT  (must not match)
    allebanon -> lebanon    0.831   SAME       (want to match)
    niger     -> nigeria    0.943   DIFFERENT  (must not match)
```

Any threshold low enough to catch `libanon` also merges Ireland into Iceland and Niger into Nigeria. There is no safe cut-off, so there is no auto-merge here. Human names are worse, not better: `Ali Hassan` and `Ali Hussain` are one edit apart and are usually two people.

## The composite metric

Three signals combined by taking the **maximum**, because each catches a failure mode the others miss:

- **Jaro-Winkler** — transpositions and shared prefixes (`egpyt`/`egypt`)
- **Levenshtein ratio** — insertions and deletions (`moroco`/`morocco`)
- **Token Jaccard** — reordering (`Habal Abdullah`/`Abdullah Habal`)

Inputs are normalised first; identical normalised strings score 1.0. Beyond a length limit (255 bytes) Levenshtein returns 0 because PHP's `levenshtein()` is byte-based and hard-capped — multibyte names that exceed it are not typo-variants of each other anyway.

## API

- `score($a, $b): float` — composite similarity in `[0.0, 1.0]`.
- `rank($needle, $candidates, $floor = 0.72, $limit = 5): array` — scores a needle against a pool, keeps candidates at or above the floor, returns them sorted by score descending. Used by the resolvers to attach suggestions to quarantine rows.

## Performance rule

`score()` is O(n·m) on string length. Never call it in a loop over a full table during import — use `MatchCandidateFinder`, which blocks first.

## The contract

Deterministic transformations may merge; probabilistic ones may only suggest. This is enforced by a regression test (`similarity_scores_of_same_and_different_entities_overlap`) that asserts distinct-country pairs still score at least as high as a genuine misspelling, so the scores can never silently separate and justify a threshold.
