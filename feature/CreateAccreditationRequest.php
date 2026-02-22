<?php

// filePath: app/Filament/Center/Resources/AccreditationRequests/Pages/CreateAccreditationRequest.php
declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccreditationRequest extends CreateRecord
{
    protected static string $resource = AccreditationRequestResource::class;

    /**
     * Inject the authenticated center's ID and force status to Pending
     * regardless of any form input — centers cannot set status themselves.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_center_id'] = auth('certified_center')->id();
        $data['status'] = AccreditationStatus::Pending->value;

        // Scrub admin-only fields — defensive measure.
        unset($data['admin_notes'], $data['reviewed_by'], $data['reviewed_at']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
