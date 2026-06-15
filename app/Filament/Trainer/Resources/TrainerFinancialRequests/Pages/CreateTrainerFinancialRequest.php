<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerFinancialRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = auth('trainer')->id();
        return $data;
    }
}
