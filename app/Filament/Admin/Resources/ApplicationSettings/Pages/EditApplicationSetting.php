<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditApplicationSetting extends EditRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
