<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainees\Pages;

use App\Filament\Center\Resources\Trainees\TraineeResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\CertifiedCenter;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TraineeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_type'] = CertifiedCenter::class;
        $data['owner_id'] = auth('certified_center')->id();

        return $data;
    }
}
