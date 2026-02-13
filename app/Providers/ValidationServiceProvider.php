<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

final class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Validator::extend('phone', function ($attribute, $value) {
            return (bool) preg_match('/^\+?[0-9]{9,15}$/', $value);
        });
    }
}
