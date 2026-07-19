<?php

declare(strict_types=1);

namespace App\Services\ApplicationSetting;

use App\Repositories\ApplicationSetting\ApplicationSettingRepository;

final class ApplicationSettingService
{
    public function __construct(private readonly ApplicationSettingRepository $applicationSettingRepository) {}

    public function getByKey(string $key, mixed $default = null): mixed
    {
        $setting = $this->applicationSettingRepository->findByKey($key);

        if (! $setting) {
            return $default;
        }

        return $setting->value;
    }
}
