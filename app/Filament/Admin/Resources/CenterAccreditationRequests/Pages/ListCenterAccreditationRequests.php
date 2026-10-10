<?php

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Admin\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListCenterAccreditationRequests extends ListRecords
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
