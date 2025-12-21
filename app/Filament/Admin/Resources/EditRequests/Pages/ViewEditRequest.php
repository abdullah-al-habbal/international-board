<?php

namespace App\Filament\Admin\Resources\EditRequests\Pages;

use App\Filament\Admin\Resources\EditRequests\EditRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEditRequest extends ViewRecord
{
    protected static string $resource = EditRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
