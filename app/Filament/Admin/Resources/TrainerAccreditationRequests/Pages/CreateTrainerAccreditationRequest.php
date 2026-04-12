<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Admin\Resources\TrainerAccreditationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerAccreditationRequest extends CreateRecord
{
    protected static string $resource = TrainerAccreditationRequestResource::class;
}
