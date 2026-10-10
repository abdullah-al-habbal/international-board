<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCertifiedCenterDocumentType extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterDocumentTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_center_id'] = Auth::guard('certified_center')->id();

        return $data;
    }
}
