<?php

declare(strict_types=1);

use App\Models\ApplicationSetting;

class Setting
{
    public static function get(string $key): ?string
    {
        return cache()->remember(
            "setting_{$key}",
            3600,
            fn() =>
            ApplicationSetting::where('key', $key)->value('value')
        );
    }
}
