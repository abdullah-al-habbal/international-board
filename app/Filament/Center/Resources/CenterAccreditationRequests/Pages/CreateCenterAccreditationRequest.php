<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Center\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCenterAccreditationRequest extends CreateRecord
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_center_id'] = auth('certified_center')->id();
        $data['status'] = AccreditationStatus::Pending->value;

        unset($data['admin_notes'], $data['reviewed_by'], $data['reviewed_at']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
