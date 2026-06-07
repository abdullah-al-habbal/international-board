<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerAccreditationRequest extends CreateRecord
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = auth('trainer')->id();
        $data['status'] = AccreditationStatus::Pending->value;

        unset($data['admin_notes'], $data['reviewed_by'], $data['reviewed_at']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
