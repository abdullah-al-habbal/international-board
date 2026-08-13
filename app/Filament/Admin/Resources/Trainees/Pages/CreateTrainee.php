<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TraineeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_type'] = User::class;
        $data['owner_id'] = auth('web')->id();

        return $data;
    }
}
