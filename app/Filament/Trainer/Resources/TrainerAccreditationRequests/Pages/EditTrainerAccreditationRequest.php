<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditTrainerAccreditationRequest extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerAccreditationRequestResource::class;
}
