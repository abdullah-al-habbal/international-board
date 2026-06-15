<?php

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCertifiedCenterDocumentType extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
