<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Center\Resources\CenterDocumentTypeRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListCenterDocumentTypeRequests extends ListRecords
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;
}
