<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ApplicationSetting;
use App\Services\StaticPage\StaticPageService;
use App\Support\LocaleConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class ViewServiceProvider extends ServiceProvider
{
    public function boot(StaticPageService $staticPageService): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        View::share('navigationPages', []);
        View::share('appSettings', collect());
        View::share('socialLinks', []);
        View::share('currentLocale', app()->getLocale());
        View::share('availableLocales', LocaleConfig::availableLocales());

        if (Schema::hasTable('static_pages')) {
            View::share('navigationPages', $staticPageService->getActivePages());
        }

        if (Schema::hasTable('application_settings')) {
            $settings = ApplicationSetting::all()->pluck('value', 'key');
            View::share('appSettings', $settings);

            $rawSocial = $settings->get('social_links', '[]');
            if (is_string($rawSocial)) {
                $rawSocial = json_decode($rawSocial, true) ?? [];
            }
            View::share('socialLinks', is_array($rawSocial) ? $rawSocial : []);
        }
    }
}
