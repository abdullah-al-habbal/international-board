<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListCertifiedCenterDocumentTypes extends ListRecords
{
    protected static string $resource = CertifiedCenterDocumentTypeResource::class;
}
