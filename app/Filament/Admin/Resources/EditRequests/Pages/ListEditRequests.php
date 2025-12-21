<?php

namespace App\Filament\Admin\Resources\EditRequests\Pages;

use App\Filament\Admin\Resources\EditRequests\EditRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEditRequests extends ListRecords
{
    protected static string $resource = EditRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
