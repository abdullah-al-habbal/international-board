<?php

declare(strict_types=1);

namespace App\Repositories\ApplicationSetting;

use App\Models\ApplicationSetting;

final class ApplicationSettingRepository
{
    public function __construct(private readonly ApplicationSetting $applicationSetting) {}

    public function findByKey(string $key): ?ApplicationSetting
    {
        return $this->applicationSetting->newQuery()->where('key', $key)->first();
    }
}
