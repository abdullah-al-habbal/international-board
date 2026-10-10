<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Pages;

use App\Filament\Center\Resources\Trainers\TrainerResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainer extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['center_id'] = auth('certified_center')->id();

        return $data;
    }
}
