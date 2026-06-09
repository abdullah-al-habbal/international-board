<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Pages;

use App\Filament\Center\Resources\Trainers\TrainerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainer extends CreateRecord
{
    protected static string $resource = TrainerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['center_id'] = auth('certified_center')->id();
        $data['is_active'] = true;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
