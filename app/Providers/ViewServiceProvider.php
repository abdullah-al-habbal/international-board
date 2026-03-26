<?php

namespace App\Providers;

use App\Models\StaticPage;
use App\Repositories\StaticPage\StaticPageRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (class_exists(StaticPage::class)) {
                $repository = app(StaticPageRepository::class);
                $view->with('staticPages', $repository->getAllActive());
            }
        });
    }
}
