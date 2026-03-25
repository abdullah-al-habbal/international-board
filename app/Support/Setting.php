<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ApplicationSetting;

class Setting
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return cache()->remember(
            "setting_{$key}",
            3600,
            fn() => ApplicationSetting::get($key, $default)
        );
    }
}
