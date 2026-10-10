<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Decimal-safe money arithmetic.
 *
 * Money columns are DECIMAL(12,2) and Eloquent hands them back as strings
 * ("1000.50") because of the `decimal:2` cast. Subtracting those with PHP
 * floats silently loses precision, so every authoritative money calculation
 * goes through this class: values are parsed into exact integer minor units
 * (cents), the arithmetic happens on integers, and the result is rendered back
 * as a fixed-scale decimal string.
 *
 * DECIMAL(12,2) tops out at 9,999,999,999.99 — 999,999,999,999 minor units —
 * which leaves an int64 seven orders of magnitude of headroom, so no value the
 * schema can hold is able to overflow.
 *
 * ext-bcmath is available in development but is deliberately not a hard
 * requirement: production is deployed with `composer install --no-dev` and
 * `script_stop: true`, so an unverifiable `ext-*` constraint would be able to
 * abort a deploy. Scaled-integer arithmetic is exact without it.
 */
final class Money
{
    public const SCALE = 2;

    /** 10 ** SCALE. */
    private const MINOR_UNIT_FACTOR = 100;

    /** Separators and spacers a localized or masked input can carry. */
    private const DISCARDED_CHARACTERS = [
        ',',            // Filament's $money mask thousands separator
        ' ',
        "\u{00A0}",     // no-break space
        "\u{202F}",     // narrow no-break space
        "\u{066C}",     // Arabic thousands separator
        "\u{2212}",     // Unicode minus sign is normalized separately below
    ];

    /** Arabic-Indic and extended Arabic-Indic digits => ASCII. */
    private const DIGIT_FOLD = [
        "\u{0660}" => '0', "\u{0661}" => '1', "\u{0662}" => '2', "\u{0663}" => '3', "\u{0664}" => '4',
        "\u{0665}" => '5', "\u{0666}" => '6', "\u{0667}" => '7', "\u{0668}" => '8', "\u{0669}" => '9',
        "\u{06F0}" => '0', "\u{06F1}" => '1', "\u{06F2}" => '2', "\u{06F3}" => '3', "\u{06F4}" => '4',
        "\u{06F5}" => '5', "\u{06F6}" => '6', "\u{06F7}" => '7', "\u{06F8}" => '8', "\u{06F9}" => '9',
        "\u{066B}" => '.', // Arabic decimal separator
    ];

    /**
     * Render a value as a fixed-scale decimal string ("1,000.5" => "1000.50").
     */
    public static function normalize(int|float|string|null $value): string
    {
        return self::fromMinorUnits(self::toMinorUnits($value));
    }

    public static function add(int|float|string|null $augend, int|float|string|null $addend): string
    {
        return self::fromMinorUnits(self::toMinorUnits($augend) + self::toMinorUnits($addend));
    }

    public static function subtract(int|float|string|null $minuend, int|float|string|null $subtrahend): string
    {
        return self::fromMinorUnits(self::toMinorUnits($minuend) - self::toMinorUnits($subtrahend));
    }

    /**
     * @return int -1 when $left < $right, 0 when equal, 1 when $left > $right
     */
    public static function compare(int|float|string|null $left, int|float|string|null $right): int
    {
        return self::toMinorUnits($left) <=> self::toMinorUnits($right);
    }

    public static function isGreaterThan(int|float|string|null $left, int|float|string|null $right): bool
    {
        return self::compare($left, $right) === 1;
    }

    public static function isPositive(int|float|string|null $value): bool
    {
        return self::toMinorUnits($value) > 0;
    }

    public static function isNegative(int|float|string|null $value): bool
    {
        return self::toMinorUnits($value) < 0;
    }

    /**
     * Exact minor-unit (cent) representation of a money value.
     */
    public static function toMinorUnits(int|float|string|null $value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_int($value)) {
            return $value * self::MINOR_UNIT_FACTOR;
        }

        if (is_float($value)) {
            // Only reachable for values supplied in PHP (factories, seeders).
            // Values read from the database arrive as strings and never take
            // this branch, so no stored amount is routed through a float.
            return (int) round($value * self::MINOR_UNIT_FACTOR);
        }

        return self::parse($value);
    }

    public static function fromMinorUnits(int $minorUnits): string
    {
        $sign = $minorUnits < 0 ? '-' : '';
        $absolute = abs($minorUnits);

        return $sign
            .intdiv($absolute, self::MINOR_UNIT_FACTOR)
            .'.'
            .str_pad((string) ($absolute % self::MINOR_UNIT_FACTOR), self::SCALE, '0', STR_PAD_LEFT);
    }

    /**
     * Parse a decimal string into minor units without touching a float.
     */
    private static function parse(string $value): int
    {
        $value = str_replace(
            array_keys(self::DIGIT_FOLD),
            array_values(self::DIGIT_FOLD),
            trim($value),
        );

        $isNegative = str_starts_with($value, '-') || str_starts_with($value, "\u{2212}");

        $value = str_replace(self::DISCARDED_CHARACTERS, '', $value);
        $value = preg_replace('/[^0-9.]/', '', $value) ?? '';

        if ($value === '') {
            return 0;
        }

        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');

        $minorUnits = (int) ($whole === '' ? '0' : $whole) * self::MINOR_UNIT_FACTOR;

        if ($fraction !== '') {
            // Round half-up on the first digit beyond the supported scale.
            $scaled = substr(str_pad($fraction, self::SCALE + 1, '0'), 0, self::SCALE + 1);
            $minorUnits += intdiv((int) $scaled + 5, 10);
        }

        return $isNegative ? -$minorUnits : $minorUnits;
    }
}
