<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerFinancialRequest extends CreateRecord
{
    protected static string $resource = TrainerFinancialRequestResource::class;
}
