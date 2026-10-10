# Name normalisation

`app/Support/Text/NameNormalizer.php` is the single source of truth for every normalised key used by the importer, the backfill command, the duplicate finder and any admin UI. One implementation in one place — three copies of "roughly the same regex" is how a dataset ends up with keys that disagree.

Requires PHP 8.5 (pipe operator) and ext-intl.

## The three outputs

One raw value produces three different strings, each with a distinct job:

| Method | Form | Stored in | Used for |
|---|---|---|---|
| `normalize()` | canonical, human-readable, one space between tokens | `name_normalized` | display, review UIs, input to fuzzy scoring |
| `key()` | canonical with ALL whitespace removed | `name_key` | the ONLY boundary at which two rows are silently treated as the same entity; sits behind a UNIQUE index |
| `blockKey()` | order-independent, article-stripped | never stored | generating candidate pairs for human review |

## Deliberate non-goal: no transliteration

This class does NOT transliterate Arabic script to Latin. Transliterating `سوريا` yields `swrya`, not `syria`, so it cannot unify a name with its English exonym — only the alias table can do that. Transliteration would add lossiness for no matching power, so each script is normalised within itself.

## Pipeline

Every step takes exactly one required argument, which is what the PHP 8.5 pipe operator requires. `normalize()` expands to:

```
trim
→ toNfkc (Normalizer::FORM_KC)
→ toLower (mb_strtolower)
→ decompose (NFD — splits base letters from their marks)
→ stripCombiningMarks (\p{M}+)
→ stripTatweel (U+0640)
→ foldArabicLetters
→ toAsciiDigits (Arabic-Indic ٠-٩ and Extended ۰-۹)
→ punctuationToSpace
→ collapseWhitespace
```

### What each step does

- **NFKC** makes canonically-equivalent strings equal before anything else runs.
- **NFD + strip marks** is the single step that handles Latin accents (`é`→`e`), Arabic harakat (`عَبْدُ`→`عبد`), AND the hamza-carrier alef forms (`أ إ آ`→`ا`, `ؤ`→`و`, `ئ`→`ي`).
- **Tatweel** (U+0640) is a stretching character carrying no meaning; removed outright.
- **Arabic folding** handles letters NFD does not separate: `ة`→`ه` (taa marbuta), `ى`→`ي` (alef maqsura), standalone `ء` removed, and the Persian `ک`→`ك`, `ی`→`ي`, `ھ`→`ه`.
- **Digits**: Arabic-Indic and Extended Arabic-Indic digits become ASCII.
- **Punctuation becomes a space rather than being deleted.** This is the difference between `al-habal`→`al habal` (matches `al habal`) and `al-habal`→`alhabal` (silently disagrees). `key()` removes the spaces afterwards, so both forms still converge.

## Key details

- `key()` = `normalize()` + remove all whitespace + cap length.
- **Length cap**: stored keys are max 255 chars (utf8mb4 at 255 = 1020 bytes, inside InnoDB's 3072-byte DYNAMIC limit). Truncating a key would let two long distinct names collide, so anything over the limit keeps a prefix plus a `sha1` hash of the whole value.
- **`articleStrippedKey()`** drops a leading `al`/`el` when at least 4 characters remain (`allebanon`→`lebanon`). Returns `null` when nothing was stripped so callers can skip a redundant lookup. Produces a candidate for an EXACT lookup only — never a fuzzy one. Precedence matters: `algeria` resolves on its exact key before it ever reaches here.
- **`keyWithoutNoise()`** removes caller-supplied junk words (`like`, `approx`, `unknown`, ...) then re-keys. Deterministic and exact — `like Syria`→`syria`. Returns `null` when nothing was removed.

## Stored as a plain column, not a generated column

Keys live in a plain indexed column deliberately, not a `PERSISTENT` generated column. Generated columns would work — `LOWER`/`REPLACE`/`REGEXP_REPLACE` are deterministic and permitted — but then the normalisation rules live in two places, and changing them means an `ALTER TABLE` on a live table instead of re-running a backfill command.

`name_key` uses `utf8mb4_bin` so comparison is exact bytes: the normalising is already done, and a collation should not make additional equality decisions on top.

## Collation note

MariaDB 11.8 defaults to utf8mb4 with UCA 14.0.0 collations, so `utf8mb4_uca1400_ai_ci` gives accent-insensitive comparison at the DB level. Not required here (the normaliser handles it) but worth having on display columns. Verify availability with `SHOW COLLATION LIKE 'utf8mb4_uca1400%'` before using it.

## PHP 8.5 pipe operator constraints

The pipe fits the pipeline precisely because every step is genuinely unary. Three constraints:

- Every callable must accept exactly one *required* parameter — multi-argument functions need a closure wrapper, since PHP's pipe has no partial application.
- By-reference parameters are rejected (`explode('-', $s) |> array_pop(...)` is an error).
- `void` returns coerce to `null`, so a logging step mid-chain silently destroys the value.

**Deployment caveat:** `|>` is a *parse* error on PHP < 8.5, not a runtime error, so a file using it fails to load entirely on an older interpreter. Confirm every path that executes this code is on 8.5 — production CLI *and* the FPM pool, which are not always the same build on Hostinger — and confirm Pint/PHPStan versions can parse it.

## Backwards-compatibility shim

`HasStringNormalization` is a deprecated shim delegating to `NameNormalizer` (used to hold the normalisation rules inline). Kept only until the last caller is migrated, then delete it.
