<?php

declare(strict_types=1);

namespace App\Support;

final class LocaleConfig
{
    private const DEFAULT_AVAILABLE_LOCALES = ['en', 'ar'];

    /**
     * @return list<string>
     */
    public static function availableLocales(): array
    {
        $locales = config('app.available_locales', self::DEFAULT_AVAILABLE_LOCALES);

        return is_array($locales) ? array_values($locales) : self::DEFAULT_AVAILABLE_LOCALES;
    }

    public static function isAvailable(string $locale): bool
    {
        return in_array($locale, self::availableLocales(), true);
    }

    public static function defaultLocale(): string
    {
        $default = config('app.locale', self::DEFAULT_AVAILABLE_LOCALES[0]);

        if (! is_string($default) || ! self::isAvailable($default)) {
            return self::DEFAULT_AVAILABLE_LOCALES[0];
        }

        return $default;
    }
}
