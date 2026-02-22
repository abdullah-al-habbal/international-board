<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    private const SESSION_KEY = 'locale';

    private const DEFAULT_AVAILABLE_LOCALES = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getRequestedLocale();

        if ($locale !== null) {
            $this->setLocale($locale);

            return $next($request);
        }

        $this->setLocale($this->getDefaultLocale());

        return $next($request);
    }

    private function getRequestedLocale(): ?string
    {
        $locale = (string) Session::get(self::SESSION_KEY);
        if (! $locale) {
            return null;
        }

        return $this->isAvailableLocale($locale) ? $locale : null;
    }

    private function setLocale(string $locale): void
    {
        App::setLocale($locale);
    }

    private function isAvailableLocale(string $locale): bool
    {
        return in_array($locale, $this->getAvailableLocales(), true);
    }

    private function getAvailableLocales(): array
    {
        $locales = config('app.available_locales', self::DEFAULT_AVAILABLE_LOCALES);

        return is_array($locales) ? $locales : self::DEFAULT_AVAILABLE_LOCALES;
    }

    private function getDefaultLocale(): string
    {
        $default = config('app.locale', self::DEFAULT_AVAILABLE_LOCALES[0]);

        return in_array($default, $this->getAvailableLocales(), true)
            ? $default
            : self::DEFAULT_AVAILABLE_LOCALES[0];
    }
}
