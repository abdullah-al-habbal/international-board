<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Pages;

use App\Filament\Admin\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccreditationRequests extends ListRecords
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
