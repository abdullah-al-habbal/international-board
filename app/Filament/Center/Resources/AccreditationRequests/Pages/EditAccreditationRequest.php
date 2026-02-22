<?php

namespace App\Filament\Center\Resources\AccreditationRequests\Pages;

use App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAccreditationRequest extends EditRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
