<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicationSetting extends ViewRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
