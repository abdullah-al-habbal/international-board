<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditApplicationSetting extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = ApplicationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
