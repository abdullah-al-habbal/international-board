<?php

// filePath: app/Filament/Center/Resources/AccreditationRequests/Pages/ViewAccreditationRequest.php
declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests\Pages;

use App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAccreditationRequest extends ViewRecord
{
    protected static string $resource = AccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions for centers on the view page.
        ];
    }
}
