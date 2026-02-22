<?php

namespace App\Filament\Admin\Resources\EditRequests\Pages;

use App\Filament\Admin\Resources\EditRequests\EditRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEditRequest extends EditRecord
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
