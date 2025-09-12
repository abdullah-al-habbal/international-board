<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    private function getLocale(Request $request): string
    {
        $supportedLocales = $this->getSupportedLocales();

        $localeFromUrl = $this->getLocaleFromUrl($request, $supportedLocales);
        if ($localeFromUrl !== null) {
            return $localeFromUrl;
        }

        $localeFromSession = $this->getLocaleFromSession($supportedLocales);
        if ($localeFromSession !== null) {
            return $localeFromSession;
        }

        $localeFromHeader = $this->getLocaleFromHeader($request, $supportedLocales);
        if ($localeFromHeader !== null) {
            return $localeFromHeader;
        }

        return $this->getDefaultLocale();
    }

    private function getSupportedLocales(): array
    {
        return ['en', 'ar'];
    }

    private function getLocaleFromUrl(Request $request, array $supportedLocales): ?string
    {
        if ($request->has('lang') && in_array($request->get('lang'), $supportedLocales, true)) {
            return $request->get('lang');
        }
        return null;
    }

    private function getLocaleFromSession(array $supportedLocales): ?string
    {
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $supportedLocales, true)) {
            return $sessionLocale;
        }
        return null;
    }

    private function getLocaleFromHeader(Request $request, array $supportedLocales): ?string
    {
        $preferredLocale = $request->getPreferredLanguage($supportedLocales);
        if ($preferredLocale && in_array($preferredLocale, $supportedLocales, true)) {
            return $preferredLocale;
        }
        return null;
    }

    private function getDefaultLocale(): string
    {
        return 'en';
    }
}
