<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditTrainerFinancialRequest extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerFinancialRequestResource::class;
}
