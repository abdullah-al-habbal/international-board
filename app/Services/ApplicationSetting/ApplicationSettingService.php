<?php

declare(strict_types=1);

namespace App\Services\ApplicationSetting;

use App\Repositories\ApplicationSetting\ApplicationSettingRepository;

final class ApplicationSettingService
{
    public function __construct(private readonly ApplicationSettingRepository $applicationSettingRepository) {}
}
