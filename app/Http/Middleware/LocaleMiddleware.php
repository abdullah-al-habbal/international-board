<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\LocaleConfig;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    private const SESSION_KEY = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getRequestedLocale();

        if ($locale !== null) {
            $this->setLocale($locale);

            return $next($request);
        }

        $this->setLocale(LocaleConfig::defaultLocale());

        return $next($request);
    }

    private function getRequestedLocale(): ?string
    {
        $locale = (string) Session::get(self::SESSION_KEY);
        if ($locale === '') {
            return null;
        }

        return LocaleConfig::isAvailable($locale) ? $locale : null;
    }

    private function setLocale(string $locale): void
    {
        App::setLocale($locale);
    }
}
