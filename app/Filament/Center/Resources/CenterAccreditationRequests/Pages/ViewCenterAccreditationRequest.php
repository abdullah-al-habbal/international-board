<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Center\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCenterAccreditationRequest extends ViewRecord
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
