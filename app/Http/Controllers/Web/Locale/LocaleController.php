<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Locale;

use App\Http\Controllers\Controller;
use App\Support\LocaleConfig;
use Illuminate\Http\RedirectResponse;

final class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        if (LocaleConfig::isAvailable($locale)) {
            session(['locale' => $locale]);
        }

        return back();
    }
}
