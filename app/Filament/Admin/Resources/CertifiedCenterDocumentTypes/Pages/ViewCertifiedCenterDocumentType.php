<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCertifiedCenterDocumentType extends ViewRecord
{
    protected static string $resource = CertifiedCenterDocumentTypeResource::class;
}
