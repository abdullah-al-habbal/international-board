<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditCertifiedCenterDocumentType extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterDocumentTypeResource::class;
}
