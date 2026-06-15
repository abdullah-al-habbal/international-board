<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Pages;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCertifiedCenter extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
