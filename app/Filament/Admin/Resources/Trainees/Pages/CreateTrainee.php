<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TraineeResource::class;
}
