<?php

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Admin\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCenterAccreditationRequest extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
