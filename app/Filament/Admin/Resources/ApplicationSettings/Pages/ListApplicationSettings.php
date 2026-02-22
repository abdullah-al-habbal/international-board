<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Pages;

use App\Filament\Admin\Resources\ApplicationSettings\ApplicationSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApplicationSettings extends ListRecords
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
