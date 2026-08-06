<?php

declare(strict_types=1);

namespace App\Support\Text;

final class NameNormalizer
{
    private const TATWEEL = "\u{0640}";

    private const MAX_KEY_LENGTH = 255;

    private const ARABIC_FOLD = [
        "\u{0629}" => "\u{0647}",

        "\u{0649}" => "\u{064A}",

        "\u{0621}" => '',

        "\u{06A9}" => "\u{0643}",

        "\u{06CC}" => "\u{064A}",

        "\u{06BE}" => "\u{0647}",

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

    public static function blockKey(string $value): string
    {
        $tokens = self::tokens($value);
        $meaningful = array_values(array_filter(
            $tokens,
            static fn (string $t): bool => ! in_array($t, self::NOISE_TOKENS, true),
        ));

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

    private static function toNfkc(string $value): string
    {
        return \Normalizer::normalize($value, \Normalizer::FORM_KC) ?: $value;
    }

    private static function toLower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    private static function decompose(string $value): string
    {
        return \Normalizer::normalize($value, \Normalizer::FORM_D) ?: $value;
    }

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

    private static function capLength(string $value): string
    {
        if (mb_strlen($value, 'UTF-8') <= self::MAX_KEY_LENGTH) {
            return $value;
        }

        return mb_substr($value, 0, self::MAX_KEY_LENGTH - 41, 'UTF-8').'~'.sha1($value);
    }
}
