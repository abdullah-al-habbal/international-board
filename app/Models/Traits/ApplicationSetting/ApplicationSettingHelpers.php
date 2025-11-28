<?php

declare(strict_types=1);

namespace App\Models\Traits\ApplicationSetting;

use App\Enums\SettingType;

trait ApplicationSettingHelpers
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value, SettingType $type = SettingType::Text): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
