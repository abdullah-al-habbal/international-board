<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    protected static string $resource =  null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
