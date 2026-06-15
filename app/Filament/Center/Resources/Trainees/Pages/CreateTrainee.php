<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainees\Pages;

use App\Filament\Center\Resources\Trainees\TraineeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TraineeResource::class;
}
