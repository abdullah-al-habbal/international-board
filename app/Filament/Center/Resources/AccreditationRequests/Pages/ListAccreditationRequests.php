<?php

namespace App\Filament\Center\Resources\AccreditationRequests\Pages;

use App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccreditationRequests extends ListRecords
{
    protected static string $resource = AccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
