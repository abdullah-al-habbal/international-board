<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

final class MacroServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Collection::macro('tapEach', function ($callback) {
            foreach ($this as $item) {
                $callback($item);
            }

            return $this;
        });
    }
}
