<?php
// app/Http/Controllers/Web/Locale/LocaleController.php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Locale;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

final class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        if (in_array($locale, config('app.available_locales', ['en', 'ar']), true)) {
            session(['locale' => $locale]);
        }

        return back();
    }
}
