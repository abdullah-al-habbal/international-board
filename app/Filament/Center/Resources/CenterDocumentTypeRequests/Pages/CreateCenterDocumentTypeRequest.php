<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Center\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCenterDocumentTypeRequest extends CreateRecord
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_center_id'] = auth()->id();
        return $data;
    }
}
