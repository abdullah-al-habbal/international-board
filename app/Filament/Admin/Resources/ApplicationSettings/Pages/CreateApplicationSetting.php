<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationSetting extends CreateRecord
{
    protected static string $resource =  ApplicationSettingResource::class;
}
