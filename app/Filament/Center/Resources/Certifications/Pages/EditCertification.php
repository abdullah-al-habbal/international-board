<?php

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Filament\Center\Resources\Certifications\CertificationResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCertification extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
