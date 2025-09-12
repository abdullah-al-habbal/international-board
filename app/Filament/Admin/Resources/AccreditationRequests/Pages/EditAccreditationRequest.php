<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Pages;

use App\Filament\Admin\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAccreditationRequest extends EditRecord
{
    protected static string $resource = AccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
