<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerFinancialRequest extends CreateRecord
{
    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = auth('trainer')->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
