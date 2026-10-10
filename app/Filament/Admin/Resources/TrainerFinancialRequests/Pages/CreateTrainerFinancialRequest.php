<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\Trainer;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerFinancialRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requestable_type'] = Trainer::class;

        return $data;
    }
}
