<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Pages;

use App\Filament\Admin\Resources\Trainers\TrainerResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\Certifications\CertificationResource;
class CreateTrainer extends CreateRecord
{
    protected static string $resource = TrainerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['accreditation_period_start'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return CertificationResource::getUrl('create', [
            'trainer_id' => $this->record->id,
        ]);
    }
}
