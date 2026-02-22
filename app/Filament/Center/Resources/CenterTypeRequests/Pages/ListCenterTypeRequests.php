<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Pages;

use App\Filament\Center\Resources\CenterTypeRequests\CenterTypeRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCenterTypeRequests extends ListRecords
{
    protected static string $resource = CenterTypeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
