<?php
// app/Providers/ViewServiceProvider.php
declare(strict_types=1);
namespace App\Providers;

use App\Models\ApplicationSetting;
use App\Services\StaticPage\StaticPageService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class ViewServiceProvider extends ServiceProvider
{
    public function boot(StaticPageService $staticPageService): void
    {
        if (! $this->app->runningInConsole()) {
            if (Schema::hasTable('static_pages')) {
                View::share('navigationPages', $staticPageService->getActivePages());
            }

            if (Schema::hasTable('application_settings')) {
                View::share('appSettings', ApplicationSetting::all()->pluck('value', 'key'));
            }

            View::share('currentLocale', app()->getLocale());
            View::share('availableLocales', config('app.locales', ['en', 'ar']));
        }
    }
}
