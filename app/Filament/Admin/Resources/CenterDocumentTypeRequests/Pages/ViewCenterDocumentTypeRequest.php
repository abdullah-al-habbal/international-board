<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Pages/ViewCenterDocumentTypeRequest.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCenterDocumentTypeRequest extends ViewRecord
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
