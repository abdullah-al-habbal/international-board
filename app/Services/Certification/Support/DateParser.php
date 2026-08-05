<?php

declare(strict_types=1);

namespace App\Services\Certification\Support;

use App\Services\Certification\Exceptions\InvalidDateException;

final class DateParser
{
    /**
     * Tolerantly parse the date formats found in production imports.
     *
     * Returns a `Y-m-d` string, or throws InvalidDateException when the
     * value cannot be unambiguously parsed into a real calendar date.
     * An empty value yields null.
     */
    public static function parse(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $clean = str_replace(['\\', '*', '-'], '/', $value);
        $clean = (string) preg_replace('/\s+/', '', $clean);
        $clean = trim($clean, '/');
        $clean = (string) preg_replace('/\b0+(\d+)\b/', '$1', $clean);

        $candidates = [];

        if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $clean, $m) === 1) {
            $candidates[] = [(int) $m[3], (int) $m[2], (int) $m[1]];
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $clean, $m) === 1) {
            $candidates[] = [(int) $m[1], (int) $m[2], (int) $m[3]];
            $candidates[] = [(int) $m[2], (int) $m[1], (int) $m[3]];
        }

        foreach ($candidates as [$day, $month, $year]) {
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        throw new InvalidDateException($value);
    }
}
