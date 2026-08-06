<?php

declare(strict_types=1);

namespace App\Support\Text;

/**
 * Script-aware normalisation for entity names (people, countries, document types).
 *
 * Produces three different strings from one raw value, each with a distinct job:
 *
 *   normalize()  "Canonical form"  — human readable, one space between tokens.
 *                                    Stored in `name_normalized`, shown in review UIs,
 *                                    and used as the input to fuzzy scoring.
 *
 *   key()        "Identity key"    — canonical form with ALL whitespace removed.
 *                                    Stored in `name_key` behind a UNIQUE index.
 *                                    This is the ONLY boundary at which two rows are
 *                                    silently treated as the same entity.
 *
 *   blockKey()   "Blocking key"    — order-independent, article-stripped.
 *                                    Never stored as unique; used only to generate
 *                                    candidate pairs for HUMAN review.
 *
 * Deliberate non-goal: this class does NOT transliterate Arabic script to Latin.
 * Transliterating "سوريا" yields "swrya", not "syria", so it cannot unify a name
 * with its English exonym — only the alias table can do that. Transliteration would
 * add lossiness for no matching power, so each script is normalised within itself.
 *
 * Requires PHP 8.5 (pipe operator) and ext-intl.
 */
final class NameNormalizer
{
    /** U+0640 ARABIC TATWEEL — a stretching character, carries no meaning. */
    private const TATWEEL = "\u{0640}";

    /**
     * Maximum stored key length. utf8mb4 at 255 chars = 1020 bytes, comfortably
     * inside InnoDB's 3072-byte index limit for DYNAMIC row format (MariaDB default).
     */
    private const MAX_KEY_LENGTH = 255;

    /**
     * Letters that NFD decomposition does not separate, so they need explicit folding.
     * Everything else (أ إ آ ؤ ئ and all harakat) is handled for free by
     * decompose-then-strip-marks.
     */
    private const ARABIC_FOLD = [
        "\u{0629}" => "\u{0647}", // ة -> ه   taa marbuta
        "\u{0649}" => "\u{064A}", // ى -> ي   alef maqsura
        "\u{0621}" => '',         // ء        standalone hamza
        "\u{06A9}" => "\u{0643}", // ک -> ك   Persian kaf
        "\u{06CC}" => "\u{064A}", // ی -> ي   Persian yeh
        "\u{06BE}" => "\u{0647}", // ھ -> ه
    ];

    /**
     * Tokens that carry no identity and are safely removable when generating a
     * BLOCKING key. Never removed from key(), because "Al" is a real name part.
     *
     * @var list<string>
     */
    private const NOISE_TOKENS = ['al', 'el', 'the', 'de', 'da', 'van', 'von', 'bin', 'ibn'];

    public static function normalize(string $value): string
    {
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
    }

    public static function key(string $value): string
    {
        return $value
            |> self::normalize(...)
            |> self::removeAllWhitespace(...)
            |> self::capLength(...);
    }

    /**
     * Order-independent key used ONLY to nominate candidate pairs for review.
     * "Habal, Abdullah Al" and "Abdullah Al-Habal" produce the same block key.
     */
    public static function blockKey(string $value): string
    {
        $tokens = self::tokens($value);
        $meaningful = array_values(array_filter(
            $tokens,
            static fn (string $t): bool => ! in_array($t, self::NOISE_TOKENS, true),
        ));

        // If a name is made entirely of noise tokens, keep the originals rather
        // than collapsing every such record into one empty block.
        $tokens = $meaningful === [] ? $tokens : $meaningful;

        sort($tokens);

        return self::capLength(implode('', $tokens));
    }

    /**
     * @return list<string>
     */
    public static function tokens(string $value): array
    {
        $normalized = self::normalize($value);

        return $normalized === '' ? [] : explode(' ', $normalized);
    }

    /**
     * Canonical form with a leading definite article dropped, e.g. "allebanon" -> "lebanon".
     *
     * Returns null when nothing was stripped, so callers can skip a redundant lookup.
     * This produces a candidate for an EXACT lookup only — never a fuzzy one. That
     * precedence matters: "algeria" resolves on its exact key before it ever reaches
     * here, so it is never mangled into "geria".
     */
    public static function articleStrippedKey(string $value): ?string
    {
        $key = self::key($value);

        foreach (['al', 'el'] as $article) {
            $length = strlen($article);

            if (str_starts_with($key, $article) && strlen($key) - $length >= 4) {
                return substr($key, $length);
            }
        }

        return null;
    }

    /**
     * Remove caller-supplied junk words ("like", "approx", "unknown"), then re-key.
     * Deterministic and exact — this is what turns "like Syria" into "syria" without
     * any fuzzy guessing.
     *
     * @param  list<string>  $noise  already-normalised lowercase tokens
     */
    public static function keyWithoutNoise(string $value, array $noise): ?string
    {
        if ($noise === []) {
            return null;
        }

        $tokens = self::tokens($value);
        $kept = array_values(array_filter(
            $tokens,
            static fn (string $t): bool => ! in_array($t, $noise, true),
        ));

        if ($kept === [] || count($kept) === count($tokens)) {
            return null;
        }

        return self::capLength(implode('', $kept));
    }

    // ---------------------------------------------------------------------
    // Pipeline steps. Each takes exactly one required argument, which is what
    // the pipe operator requires.
    // ---------------------------------------------------------------------

    private static function toNfkc(string $value): string
    {
        return \Normalizer::normalize($value, \Normalizer::FORM_KC) ?: $value;
    }

    private static function toLower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /** NFD splits base letters from their marks so the marks can be dropped wholesale. */
    private static function decompose(string $value): string
    {
        return \Normalizer::normalize($value, \Normalizer::FORM_D) ?: $value;
    }

    /**
     * Drops every Unicode combining mark. This single step handles Latin accents
     * (é -> e), Arabic harakat (عَبْدُ -> عبد), AND the hamza-carrier alef forms
     * (أ إ آ -> ا, ؤ -> و, ئ -> ي), because NFD decomposes all of them.
     */
    private static function stripCombiningMarks(string $value): string
    {
        return preg_replace('/\p{M}+/u', '', $value) ?? $value;
    }

    private static function stripTatweel(string $value): string
    {
        return str_replace(self::TATWEEL, '', $value);
    }

    private static function foldArabicLetters(string $value): string
    {
        return strtr($value, self::ARABIC_FOLD);
    }

    /** Arabic-Indic (٠-٩) and Extended Arabic-Indic (۰-۹) digits to ASCII. */
    private static function toAsciiDigits(string $value): string
    {
        static $map = null;

        if ($map === null) {
            $map = [];

            for ($i = 0; $i < 10; $i++) {
                $map[mb_chr(0x0660 + $i, 'UTF-8')] = (string) $i;
                $map[mb_chr(0x06F0 + $i, 'UTF-8')] = (string) $i;
            }
        }

        return strtr($value, $map);
    }

    /**
     * Punctuation becomes a space rather than being deleted. This is the difference
     * between "al-habal" -> "al habal" (correct, matches "al habal") and
     * "al-habal" -> "alhabal" (wrong, silently disagrees with "al habal").
     * key() removes the spaces afterwards, so both forms still converge.
     */
    private static function punctuationToSpace(string $value): string
    {
        return preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value) ?? $value;
    }

    private static function collapseWhitespace(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    private static function removeAllWhitespace(string $value): string
    {
        return preg_replace('/\s+/u', '', $value) ?? $value;
    }

    /**
     * Truncating a key would let two long distinct names collide, so anything over
     * the limit keeps a prefix plus a hash of the whole value.
     */
    private static function capLength(string $value): string
    {
        if (mb_strlen($value, 'UTF-8') <= self::MAX_KEY_LENGTH) {
            return $value;
        }

        return mb_substr($value, 0, self::MAX_KEY_LENGTH - 41, 'UTF-8').'~'.sha1($value);
    }
}
