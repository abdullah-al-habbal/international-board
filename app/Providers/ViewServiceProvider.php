<?php
// app/Providers/ViewServiceProvider.php
declare(strict_types=1);
namespace App\Providers;

use App\Services\StaticPage\StaticPageService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class ViewServiceProvider extends ServiceProvider
{
    public function boot(StaticPageService $staticPageService): void
    {
        View::share('navigationPages', $staticPageService->getActivePages());
        View::share('currentLocale', app()->getLocale());
        View::share('availableLocales', config('app.locales', ['en', 'ar']));
    }
}
