<?php

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Admin\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCenterAccreditationRequest extends EditRecord
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
