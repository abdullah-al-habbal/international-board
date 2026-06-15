<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationSetting extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = ApplicationSettingResource::class;
}
