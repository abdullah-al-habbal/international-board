<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Pages;

use App\Filament\Admin\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAccreditationRequest extends ViewRecord
{
    protected static string $resource = AccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
